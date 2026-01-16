<?php

namespace App\Http\Controllers;

use App\Models\PembayaranKas;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Response;

class VerificationController extends Controller
{
    private const STATUS_PENDING = 'pending';
    private const STATUS_CONFIRMED = 'confirmed';
    private const STATUS_REJECTED = 'rejected';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman verifikasi (daftar pending + statistik)
     */
    public function index()
    {
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $org = Organization::findOrFail($orgId);

        // list pembayaran pending
        // list pembayaran pending (paksa bawa kolom foto user)
        $userSelect = ['id', 'name'];

        // sesuaikan kolom yang ada di tabel users kamu
        if (Schema::hasColumn('users', 'photo')) {
            $userSelect[] = 'photo';
        }
        if (Schema::hasColumn('users', 'profile_photo_path')) {
            $userSelect[] = 'profile_photo_path';
        }
        if (Schema::hasColumn('users', 'avatar')) {
            $userSelect[] = 'avatar';
        }

        // ambil nama tagihan (kolom di tabel bills biasanya: id, name, period)
        $billSelect = ['id'];
        if (Schema::hasTable('bills')) {
            if (Schema::hasColumn('bills', 'name')) {
                $billSelect[] = 'name';
            }
            if (Schema::hasColumn('bills', 'period')) {
                $billSelect[] = 'period';
            }
        }


        $pembayaran = PembayaranKas::query()
            ->with([
                'user' => function ($q) use ($userSelect) {
                    $q->select($userSelect);
                },
                'bill' => function ($q) use ($billSelect) {
                    $q->select($billSelect);
                },
            ])
            ->where('organization_id', $org->id)
            ->where('status', self::STATUS_PENDING)
            ->latest()
            ->paginate(10);


        // statistik
        $pendingCount = PembayaranKas::where('organization_id', $org->id)
            ->where('status', self::STATUS_PENDING)
            ->count();

        $approvedTodayCount = PembayaranKas::where('organization_id', $org->id)
            ->where('status', self::STATUS_CONFIRMED)
            ->whereNotNull('verified_at')
            ->whereDate('verified_at', Carbon::today())
            ->count();

        $rejectedTodayCount = PembayaranKas::where('organization_id', $org->id)
            ->where('status', self::STATUS_REJECTED)
            // some flows set verified_at on rejection; if you use updated_at, change this accordingly
            ->whereNotNull('verified_at')
            ->whereDate('verified_at', Carbon::today())
            ->count();

        return view('VerifikasiPembayaran', compact(
            'org',
            'pembayaran',
            'pendingCount',
            'approvedTodayCount',
            'rejectedTodayCount'
        ));
    }

    /**
     * Approve pembayaran (hanya admin organisasi)
     */
    public function approve(Request $request, PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');
        if ($pembayaranKas->organization_id != $orgId) abort(403);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAdmin = $user->organizations()
            ->where('organizations.id', $orgId)
            ->wherePivot('role', 'admin')
            ->exists();

        if (! $isAdmin) abort(403);

        DB::beginTransaction();
        try {
            $pembayaranKas->status = self::STATUS_CONFIRMED;

            if (Schema::hasColumn('pembayaran_kas', 'verified_by')) {
                $pembayaranKas->verified_by = Auth::id();
            }
            if (Schema::hasColumn('pembayaran_kas', 'verified_at')) {
                $pembayaranKas->verified_at = now();
            }

            $pembayaranKas->save();

            DB::commit();
            return back()->with('success', 'Pembayaran disetujui.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Gagal approve pembayaran: ' . $e->getMessage(), [
                'id' => $pembayaranKas->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors('Gagal menyetujui pembayaran.');
        }
    }

    public function reject(Request $request, PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');
        if ($pembayaranKas->organization_id != $orgId) abort(403);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAdmin = $user->organizations()
            ->where('organizations.id', $orgId)
            ->wherePivot('role', 'admin')
            ->exists();

        if (! $isAdmin) abort(403);

        DB::beginTransaction();
        try {
            $pembayaranKas->status = self::STATUS_REJECTED;

            if (Schema::hasColumn('pembayaran_kas', 'verified_by')) {
                $pembayaranKas->verified_by = Auth::id();
            }
            if (Schema::hasColumn('pembayaran_kas', 'verified_at')) {
                $pembayaranKas->verified_at = now();
            }

            // optional alasan penolakan
            if (Schema::hasColumn('pembayaran_kas', 'rejection_reason')) {
                $pembayaranKas->rejection_reason = $request->input('reason', 'Ditolak oleh bendahara');
            }

            $pembayaranKas->save();

            DB::commit();
            return back()->with('success', 'Pembayaran ditolak.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Gagal reject pembayaran: ' . $e->getMessage(), [
                'id' => $pembayaranKas->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors('Gagal menolak pembayaran.');
        }
    }


    /**
     * Download receipt (admin)
     */
    public function download(PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        if ((int) $pembayaranKas->organization_id !== (int) $orgId) {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAdmin = $user->organizations()
            ->whereKey($orgId)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_if(! $isAdmin, 403);

        $path = $pembayaranKas->receipt_path;
        abort_if(! $path, 404);

        abort_if(! Storage::disk('public')->exists($path), 404);

        return response()->download(storage_path('app/public/' . $path));
    }

    public function showReceipt(PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        // Pastikan transaksi milik organisasi aktif
        abort_if((int) $pembayaranKas->organization_id !== (int) $orgId, 403);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pastikan admin/bendahara
        $isAdmin = $user->organizations()
            ->whereKey($orgId)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_if(! $isAdmin, 403);

        $path = $pembayaranKas->receipt_path;
        abort_if(! $path, 404);

        // Normalisasi path (hindari dobel prefix)
        $path = ltrim($path, '/');
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        abort_if(! Storage::disk('public')->exists($path), 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $fullPath = $disk->path($path);
        $mime     = $disk->mimeType($path) ?: 'application/octet-stream';
        $fileName = basename($path);
        
        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
