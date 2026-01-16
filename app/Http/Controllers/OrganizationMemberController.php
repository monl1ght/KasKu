<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\JoinRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;


class OrganizationMemberController extends Controller
{

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select')->with('error', 'Silakan pilih organisasi terlebih dahulu.');
        }

        // pastikan user anggota org
        $org = $user->organizations()->where('organizations.id', $orgId)->first();
        if (! $org) {
            abort(403, 'Anda tidak memiliki akses ke organisasi ini.');
        }

        // ✅ kunci halaman ini hanya admin
        if (Gate::denies('manageMembers', $org)) {
            abort(403);
        }

        $membersQuery = $org->users()->withPivot('role');

        if ($search = $request->query('search')) {
            $membersQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.nim', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $membersQuery->where('users.status', $status);
        }

        $members = $membersQuery->orderBy('users.name')->paginate(10)->withQueryString();

        return view('ManajemenAnggota', [
            'members' => $members,
            'org' => $org,
        ]);
    }
    /**
     * Show -> redirect to profile.show (re-use profile controller/view).
     */
    public function show(Request $request, User $user)
    {
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        // pastikan org valid & user yang login memang anggota org tsb
        $org = $request->user()
            ->organizations()
            ->where('organizations.id', $orgId)
            ->firstOrFail();

        // ambil member + pivot (role, joined_at)
        $member = $org->users()
            ->where('users.id', $user->id)
            ->first();

        if (! $member) {
            return back()->with('error', 'Anggota tidak ditemukan di organisasi ini.');
        }

        return view('members.show', [
            'org' => $org,
            'member' => $member, // <-- PENTING: ini yang punya pivot
        ]);
    }



    /**
     * Edit form for admin (edit anggota)
     */
    public function edit(Request $request, User $user)
    {
        $current = $request->user();
        $orgId = session('active_organization_id');
        if (! $orgId) return redirect()->route('organization.select');

        $org = $current->organizations()->where('organizations.id', $orgId)->firstOrFail();

        if (Gate::denies('manageMembers', $org)) {
            abort(403);
        }

        // Pastikan target anggota masih di org
        $membership = $user->organizations()->where('organizations.id', $orgId)->first();
        if (! $membership) return back()->with('error', 'Anggota tidak ditemukan di organisasi ini.');

        // return simple edit view
        return view('members.edit', [
            'member' => $user,
            'org' => $org,
            'membership' => $membership,
        ]);
    }


    /**
     * Export members to CSV
     */
    public function export(Request $request)
    {
        $user = $request->user();
        $orgId = session('active_organization_id');
        if (! $orgId) return redirect()->route('organization.select');

        $org = $user->organizations()->where('organizations.id', $orgId)->firstOrFail();

        $membersQuery = $org->users()->withPivot('role')->orderBy('users.name');

        if ($s = $request->query('search')) {
            $membersQuery->where(function ($q) use ($s) {
                $q->where('users.name', 'like', "%{$s}%")
                    ->orWhere('users.email', 'like', "%{$s}%")
                    ->orWhere('users.nim', 'like', "%{$s}%");
            });
        }

        if ($status = $request->query('status')) {
            $membersQuery->where('users.status', $status);
        }

        $response = new StreamedResponse(function () use ($membersQuery) {
            $handle = fopen('php://output', 'w');
            echo "\xEF\xBB\xBF"; // BOM for Excel UTF-8

            fputcsv($handle, ['Nama', 'NIM', 'Email', 'Phone', 'Status', 'Role', 'Bergabung']);

            $membersQuery->chunk(200, function ($members) use ($handle) {
                foreach ($members as $m) {
                    fputcsv($handle, [
                        $m->name,
                        $m->nim ?? '',
                        $m->email,
                        $m->phone ?? '',
                        $m->status ?? '',
                        $m->pivot->role ?? '',
                        optional($m->pivot->created_at ?? $m->created_at)->format('Y-m-d'),
                    ]);
                }
            });

            fclose($handle);
        });

        $filename = 'members_' . now()->format('Ymd_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        return $response;
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        $orgId = session('active_organization_id');
        $currentUser = $request->user();

        if (! $orgId) return back()->with('error', 'Organisasi aktif tidak ditemukan.');

        $org = $currentUser->organizations()->where('organizations.id', $orgId)->firstOrFail();

        if (Gate::denies('manageMembers', $org)) {
            abort(403);
        }

        $membership = $user->organizations()->where('organizations.id', $orgId)->first();
        if (! $membership) return back()->with('error', 'Anggota tidak ditemukan di organisasi ini.');

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan diri sendiri.');
        }

        $user->status = 'tidak_aktif';
        $user->save();

        return back()->with('success', 'Anggota dinonaktifkan.');
    }

    public function kick(Request $request, User $user): RedirectResponse
    {
        $orgId = session('active_organization_id');
        $currentUser = $request->user();

        if (! $orgId) return back()->with('error', 'Organisasi aktif tidak ditemukan.');

        $org = $currentUser->organizations()->where('organizations.id', $orgId)->firstOrFail();

        if (Gate::denies('manageMembers', $org)) {
            abort(403);
        }

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Anda tidak bisa mengeluarkan diri sendiri.');
        }

        $membership = $user->organizations()->where('organizations.id', $orgId)->first();
        if (! $membership) return back()->with('error', 'Anggota tidak ditemukan di organisasi ini.');

        // prevent removing last admin
        $isAdmin = ($membership->pivot->role ?? null) === 'admin';
        if ($isAdmin) {
            $adminCount = DB::table('organization_user')
                ->where('organization_id', $orgId)
                ->where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak bisa keluarkan admin terakhir.');
            }
        }

        DB::transaction(function () use ($user, $orgId) {
            $user->organizations()->detach($orgId);
        });

        return back()->with('success', 'Anggota berhasil dikeluarkan.');
    }
    public function joinByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $code = strtoupper(trim($request->input('code')));

        $org = Organization::whereRaw('UPPER(code) = ?', [$code])->first();
        if (! $org) {
            return back()->with('error', 'Kode organisasi tidak ditemukan.');
        }

        $orgId = $org->id;

        // sudah anggota → langsung masuk
        if ($user->organizations()->where('organizations.id', $orgId)->exists()) {
            session(['active_organization_id' => $orgId]);
            return redirect()->route('member.dashboard')->with('success', 'Masuk sebagai anggota.');
        }

        // hitung admin
        $adminCount = DB::table('organization_user')
            ->where('organization_id', $orgId)
            ->where('role', 'admin')
            ->count();

        // === KASUS ORG BARU (AUTO ADMIN) ===
        if ($adminCount === 0) {
            DB::transaction(function () use ($user, $orgId) {
                $user->organizations()->attach($orgId, ['role' => 'admin']);

                JoinRequest::updateOrCreate(
                    [
                        'organization_id' => $orgId,
                        'user_id' => $user->id,
                    ],
                    [
                        'status' => 'approved',
                        'handled_by' => $user->id,
                        'handled_at' => now(),
                    ]
                );
            });

            session(['active_organization_id' => $orgId]);
            return redirect()->route('dashboard')
                ->with('success', 'Organisasi dibuat / Anda otomatis menjadi Bendahara.');
        }

        // === KASUS NORMAL ===
        JoinRequest::updateOrCreate(
            [
                'organization_id' => $orgId,
                'user_id' => $user->id,
            ],
            [
                'status' => 'pending',
            ]
        );

        return back()->with(
            'info',
            'Permintaan bergabung berhasil dikirim. Menunggu persetujuan bendahara.'
        );
    }

    public function approveJoin(Request $request, JoinRequest $joinRequest)
    {
        $orgId = session('active_organization_id');

        if ($joinRequest->organization_id !== $orgId) {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        DB::transaction(function () use ($joinRequest, $orgId, $user) {
            $joinRequest->user->organizations()->attach($orgId, [
                'role' => 'member'
            ]);

            $joinRequest->update([
                'status' => 'approved',
                'handled_at' => now(),
                'handled_by' => $user->id,
            ]);
        });

        return back()->with('success', 'Anggota berhasil disetujui.');
    }

    public function join(Request $request)
    {
        $user = $request->user();
        $org = Organization::where('code', $request->code)->firstOrFail();

        if ($user->organizations()->where('organizations.id', $org->id)->exists()) {

            // 🔥 PAKSA SESSION DISIMPAN
            session()->forget('active_organization_id');
            session()->put('active_organization_id', $org->id);
            session()->save();

            // 🔥 REDIRECT LANGSUNG KE MEMBER DASHBOARD
            return redirect()->to('/member/dashboard');
        }

        // kalau belum anggota → buat join request
    }

    public function activate(Request $request, User $user): RedirectResponse
    {
        $orgId = session('active_organization_id');
        $currentUser = $request->user();

        if (! $orgId) {
            return back()->with('error', 'Organisasi aktif tidak ditemukan.');
        }

        $org = $currentUser->organizations()->where('organizations.id', $orgId)->firstOrFail();

        if (Gate::denies('manageMembers', $org)) {
            abort(403);
        }

        $membership = $user->organizations()->where('organizations.id', $orgId)->first();
        if (! $membership) {
            return back()->with('error', 'Anggota tidak ditemukan di organisasi ini.');
        }

        $user->status = 'aktif';
        $user->save();

        return back()->with('success', 'Anggota berhasil diaktifkan.');
    }
}
