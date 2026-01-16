<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrganizationUpdateRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreOrganizationRequest;
use App\Models\PembayaranKas;
use Throwable;


use App\Models\BankAccount;
use App\Models\Ewallet;

class OrganizationController extends Controller
{
    /**
     * =========================
     * BUAT ORGANISASI BARU
     * =========================
     */
    public function store(Request $request)
    {
        // Validasi: sesuaikan dengan apa yang ada di form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // jika kamu memang ingin wajibkan nomor rekening organisasi,
            // tambahkan rule ini dan sediakan input di form:
            // 'account_number' => 'nullable|numeric',
            'banks' => 'nullable|array',
            'banks.*.bank_name' => 'nullable|string|max:100',
            'banks.*.number' => 'nullable|string|max:100',
            'banks.*.owner_name' => 'nullable|string|max:150',
            'ewallets' => 'nullable|array',
            'ewallets.*.type' => 'nullable|string|max:100',
            'ewallets.*.number' => 'nullable|string|max:100',
            'ewallets.*.owner_name' => 'nullable|string|max:150',
        ]);

        // Gunakan transaction agar konsisten
        DB::transaction(function () use ($validated, $request, &$org) {
            // generate kode unik
            $code = 'KASKU-' . strtoupper(Str::random(4));

            $org = Organization::create([
                'name' => $validated['name'],
                'code' => $code,
                // 'account_number' => $validated['account_number'] ?? null, // opsional
            ]);

            // attach user sebagai admin (sangat penting)
            $org->users()->attach(Auth::id(), ['role' => 'admin']);

            // jika ada banks, simpan
            if ($request->filled('banks') && is_array($request->banks)) {
                foreach ($request->banks as $bank) {
                    // pastikan paling tidak ada nomor atau nama bank
                    if (!empty($bank['bank_name']) || !empty($bank['number'])) {
                        $org->bankAccounts()->create([
                            'bank_name' => $bank['bank_name'] ?? null,
                            'number' => $bank['number'] ?? null,
                            'owner_name' => $bank['owner_name'] ?? null,
                        ]);
                    }
                }
            }

            // jika ada ewallets, simpan
            if ($request->filled('ewallets') && is_array($request->ewallets)) {
                foreach ($request->ewallets as $ew) {
                    if (!empty($ew['type']) && !empty($ew['number'])) {
                        $org->ewallets()->create([
                            'provider'   => $ew['type'],        // pasti tidak null
                            'number'     => $ew['number'],
                            'owner_name' => $ew['owner_name'] ?? null,
                        ]);
                    }
                }
            }

            // set organisasi aktif di session agar langsung muncul di dashboard/select
            session(['active_organization_id' => $org->id]);
        });

        // redirect ke halaman select atau dashboard sesuai alur aplikasi
        return redirect()
            ->route('organization.select') // tampilkan daftar organisasi (kamu menampilkan yang role=admin)
            ->with('success', 'Organisasi berhasil dibuat dan diaktifkan.');
    }
    /**
     * =========================
     * HALAMAN PILIH ORGANISASI
     * =========================
     */
    public function select()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $organizations = $user->organizations()->get();

        if ($organizations->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum tergabung dalam organisasi manapun.');
        }

        // ✅ AUTO AKTIFKAN JIKA BELUM ADA SESSION
        if (! session('active_organization_id')) {
            session([
                'active_organization_id' => $organizations->first()->id,
            ]);

            return redirect()->route('member.dashboard');
        }

        return view('organization.select', compact('organizations'));
    }

    /**
     * =========================
     * AKTIFKAN ORGANISASI
     * =========================
     */
    public function activate(Organization $organization)
    {
        abort_unless(
            $organization->users()
                ->where('users.id', Auth::id())
                ->exists(),
            403,
            'Anda bukan anggota organisasi ini.'
        );

        session(['active_organization_id' => $organization->id]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Organisasi berhasil diaktifkan.');
    }

    /**
     * =========================
     * GABUNG ORGANISASI (VIA KODE)
     * =========================
     */
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $organization = Organization::where('code', $request->code)->first();

        if (! $organization) {
            return back()->withErrors([
                'code' => 'Kode organisasi tidak ditemukan.',
            ]);
        }

        // Jika sudah anggota → langsung aktifkan
        if ($organization->users()->where('users.id', Auth::id())->exists()) {
            session(['active_organization_id' => $organization->id]);

            return redirect()->route('member.dashboard');
        }

        // Simpan permintaan join
        JoinRequest::firstOrCreate([
            'organization_id' => $organization->id,
            'user_id'         => Auth::id(),
        ]);

        return back()->with(
            'success',
            'Permintaan bergabung berhasil dikirim.'
        );
    }
    public function edit()
    {
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $organization = Organization::findOrFail($orgId);

        return view('PengaturanOrganisasi', compact('organization'));
    }


    public function update(Request $request)
    {
        $orgId = session('active_organization_id');

        if (!$orgId) {
            abort(404, 'Organisasi aktif tidak ditemukan');
        }

        $organization = Organization::findOrFail($orgId);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'email'      => 'nullable|email',
            'phone'      => 'nullable|string|max:30',
            'address'    => 'nullable|string',
            'logo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')
                ->store('logos', 'public');
        }

        $organization->update($validated);

        return redirect()
            ->route('organization.edit')
            ->with('success', 'Informasi organisasi berhasil diperbarui.');
    }
    public function updateLogo(Request $request, \App\Models\Organization $organization)
    {
        // (opsional) kalau ada policy/role bendahara, taruh authorize di sini
        // $this->authorize('update', $organization);

        $data = $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        // hapus logo lama kalau ada
        if ($organization->logo_path && Storage::disk('public')->exists($organization->logo_path)) {
            Storage::disk('public')->delete($organization->logo_path);
        }

        $path = $request->file('logo')->store('organizations/logos', 'public');

        $organization->update([
            'logo_path' => $path,
        ]);

        return back()->with('success', 'Logo organisasi berhasil diperbarui.');
    }

    public function destroy(Request $request, ?Organization $organization = null)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        // Kalau suatu saat route destroy pakai parameter {organization}, tetap aman:
        if ($organization && (int) $organization->id !== (int) $orgId) {
            abort(403, 'Organisasi aktif tidak cocok.');
        }

        // Karena route kamu saat ini TIDAK mengirim {organization},
        // maka ambil org dari session.
        $organization = $organization ?: Organization::findOrFail($orgId);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Hanya admin/bendahara (role=admin) yang boleh hapus
        $isAdmin = $user->organizations()
            ->whereKey($organization->id)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_if(! $isAdmin, 403);

        $disk = Storage::disk('public');

        // helper normalisasi path (hindari dobel prefix public/storage)
        $normalizePath = function (?string $path): ?string {
            $path = (string) $path;
            if ($path === '') return null;

            // jangan sentuh URL/data-uri
            if (preg_match('/^(https?:\/\/|data:)/i', $path)) {
                return null;
            }

            $path = ltrim($path, '/');
            $path = preg_replace('#^public/#', '', $path);
            $path = preg_replace('#^storage/#', '', $path);

            return $path ?: null;
        };

        DB::beginTransaction();
        try {
            // 1) Hapus logo file (kalau ada)
            $logoPath = $normalizePath($organization->logo_path);
            if ($logoPath && $disk->exists($logoPath)) {
                $disk->delete($logoPath);
            }

            // 2) Hapus semua file bukti (receipt_path) milik org ini
            //    (pembayaran + pengeluaran kamu sama-sama tersimpan di pembayaran_kas)
            PembayaranKas::query()
                ->where('organization_id', $organization->id)
                ->whereNotNull('receipt_path')
                ->select(['id', 'receipt_path'])
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($disk, $normalizePath) {
                    foreach ($rows as $row) {
                        $p = $normalizePath($row->receipt_path);
                        if ($p && $disk->exists($p)) {
                            $disk->delete($p);
                        }
                    }
                });

            // 3) Lepas relasi pivot anggota (aman walaupun FK cascade sudah ada)
            $organization->users()->detach();

            // 4) Hapus organization (relasi lain yang FK cascade akan ikut kehapus)
            $organization->delete();

            DB::commit();

            // 5) Bersihkan session org aktif
            session()->forget('active_organization_id');

            return redirect()
                ->route('organization.select')
                ->with('success', 'Organisasi berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return back()->with('error', 'Gagal menghapus organisasi: ' . $e->getMessage());
        }
    }
}
