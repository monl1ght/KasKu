<?php

namespace App\Http\Controllers;

use App\Models\PembayaranKas;
use App\Models\Organization;
use App\Models\BankAccount;
use App\Models\Ewallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentSubmitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;

class PembayaranKasController extends Controller
{
    // Status constants
    private const STATUS_PENDING   = 'pending';
    private const STATUS_CONFIRMED = 'confirmed';
    private const STATUS_REJECTED  = 'rejected';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman anggota: list tagihan yang bisa dibayar + stats + rekening/ewallet tujuan + riwayat pembayaran
     */
    public function index()
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $org = Organization::findOrFail($orgId);

        // 1) Tagihan yang tampil ke anggota:
        // - milik organisasi ini
        // - BELUM pernah dibayar oleh user (pending/confirmed)
        // - TANPA filter kadaluarsa (no due_date filter)
        $bills = $org->bills()
            ->whereDoesntHave('payments', function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
            })
            ->orderBy('due_date', 'asc')
            ->get();

        // 2) Riwayat pembayaran anggota (paging)
        $pembayaran = PembayaranKas::with(['bill', 'user'])
            ->where('organization_id', $org->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // 3) Statistik
        // Total tagihan (semua bill org) - TANPA kadaluarsa
        $allBills = $org->bills()->get();
        $totalTagihan = (float) $allBills->sum('amount');

        // Total dibayar (confirmed)
        $totalDibayar = (float) PembayaranKas::where('organization_id', $org->id)
            ->where('user_id', Auth::id())
            ->where('status', self::STATUS_CONFIRMED)
            ->sum('amount');

        $sisa = max(0, $totalTagihan - $totalDibayar);

        // 4) Rekening & Ewallet tujuan (ambil yang terbaru / primary)
        // Kalau punya kolom is_primary, kamu bisa pakai orderByDesc('is_primary') dulu
        $banks = BankAccount::where('organization_id', $org->id)
            ->latest()
            ->get();

        $ewallets = Ewallet::where('organization_id', $org->id)
            ->latest()
            ->get();

        return view('member.PembayaranKas', compact(
            'org',
            'bills',
            'pembayaran',
            'totalTagihan',
            'totalDibayar',
            'sisa',
            'banks',
            'ewallets'
        ));
    }

    /**
     * Form create (opsional)
     */
    public function create()
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $org = Organization::findOrFail($orgId);

        $bills = $org->bills()
            ->whereDoesntHave('payments', function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
            })
            ->orderBy('due_date', 'asc')
            ->get();

        return view('member.pembayaran_kas.create', compact('org', 'bills'));
    }

    /**
     * Simpan pembayaran
     */
    public function store(Request $request)
    {
        $org = Organization::find(session('active_organization_id'));
        if (! $org) {
            abort(404, 'Organization not found');
        }

        // Pastikan user adalah anggota organisasi
        if (! $org->users()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Anda bukan anggota organisasi ini.');
        }

        $billIdRule = Schema::hasColumn('pembayaran_kas', 'bill_id')
            ? 'required|exists:bills,id'
            : 'nullable|exists:bills,id';

        $data = $request->validate([
            'amount' => 'required',
            'bill_id' => $billIdRule,
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            'currency' => 'nullable|string|max:10',
        ]);

        // Pastikan bill milik organisasi
        if (! empty($data['bill_id'])) {
            $bill = $org->bills()->where('id', $data['bill_id'])->first();
            if (! $bill) {
                return back()->withInput()->withErrors(['bill_id' => 'Tagihan tidak ditemukan untuk organisasi ini.']);
            }
        }

        // Cek duplikasi pembayaran (pending/confirmed) untuk bill yang sama
        if (! empty($data['bill_id'])) {
            $exists = PembayaranKas::where('user_id', Auth::id())
                ->where('bill_id', $data['bill_id'])
                ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors(['bill_id' => 'Tagihan ini sudah Anda bayar atau sedang menunggu verifikasi.']);
            }
        }

        // normalisasi amount
        $data['amount'] = $this->normalizeAmount($data['amount']);

        $data['organization_id'] = $org->id;
        $data['user_id'] = Auth::id();
        $data['status'] = self::STATUS_PENDING;

        if (Schema::hasColumn('pembayaran_kas', 'currency')) {
            $data['currency'] = $request->input('currency', $data['currency'] ?? 'IDR');
        } else {
            unset($data['currency']);
        }

        if (! Schema::hasColumn('pembayaran_kas', 'bill_id')) {
            unset($data['bill_id']);
        }

        if (empty($data['bill_id'])) {
            Log::warning('Pembayaran dibuat tanpa bill_id', [
                'user_id' => Auth::id(),
                'organization_id' => $org->id,
                'amount' => $data['amount'] ?? null,
                'ip' => $request->ip(),
            ]);
        }

        $storedPath = null;
        $pembayaran = null;

        DB::beginTransaction();
        try {
            if ($request->hasFile('receipt')) {
                $storedPath = $request->file('receipt')->store('receipts', 'public');
                $data['receipt_path'] = $storedPath;
            }

            $pembayaran = PembayaranKas::create($data);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Gagal menyimpan pembayaran kas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            throw $e;
        }

        // Notifikasi ke Admin
        try {
            $admins = $org->users()->wherePivot('role', 'admin')->get();
            if ($pembayaran && $admins->isNotEmpty()) {
                if (class_exists(PaymentSubmitted::class)) {
                    Notification::send($admins, new PaymentSubmitted($pembayaran));
                } else {
                    Log::warning('Class PaymentSubmitted tidak ditemukan.');
                }
            }
        } catch (Throwable $e) {
            Log::error('Gagal mengirim notifikasi pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('member.PembayaranKas')
            ->with('success', 'Pembayaran dikirim dan menunggu verifikasi.');
    }

    /**
     * Update pembayaran (hanya pemilik & status pending)
     */
    public function update(Request $request, PembayaranKas $pembayaranKas)
    {
        if ($pembayaranKas->user_id !== Auth::id()) {
            abort(403, 'Tidak diizinkan mengedit pembayaran ini.');
        }

        if ($pembayaranKas->status !== self::STATUS_PENDING) {
            return back()->withErrors('Hanya pembayaran berstatus pending yang dapat diedit.');
        }

        $billIdRule = Schema::hasColumn('pembayaran_kas', 'bill_id')
            ? 'required|exists:bills,id'
            : 'nullable|exists:bills,id';

        $data = $request->validate([
            'bill_id' => $billIdRule,
            'amount' => 'required',
            'type' => 'nullable|string|max:191',
            'description' => 'nullable|string|max:2000',
            'payment_date' => 'nullable|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $data['amount'] = $this->normalizeAmount($data['amount']);

        if (! is_numeric($data['amount']) || $data['amount'] <= 0) {
            return back()->withInput()->withErrors(['amount' => 'Nominal pembayaran tidak valid atau nol.']);
        }

        if (! empty($data['bill_id'])) {
            $org = $pembayaranKas->organization;
            $bill = $org->bills()->where('id', $data['bill_id'])->first();
            if (! $bill) {
                return back()->withInput()->withErrors(['bill_id' => 'Tagihan tidak ditemukan untuk organisasi ini.']);
            }

            $exists = PembayaranKas::where('user_id', Auth::id())
                ->where('bill_id', $data['bill_id'])
                ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
                ->where('id', '!=', $pembayaranKas->id)
                ->exists();

            if ($exists) {
                return back()->withInput()->withErrors(['bill_id' => 'Tagihan ini sudah Anda bayar atau sedang menunggu verifikasi.']);
            }
        }

        DB::transaction(function () use ($data, $request, $pembayaranKas) {
            if ($request->hasFile('receipt')) {
                if ($pembayaranKas->receipt_path) {
                    Storage::disk('public')->delete($pembayaranKas->receipt_path);
                }
                $path = $request->file('receipt')->store('receipts', 'public');
                $data['receipt_path'] = $path;
            }

            if (! Schema::hasColumn('pembayaran_kas', 'bill_id')) {
                unset($data['bill_id']);
            }

            $pembayaranKas->update($data);
        });

        return redirect()->route('member.PembayaranKas')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Hapus pembayaran (owner atau admin)
     */
    public function destroy(PembayaranKas $pembayaranKas)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAdmin = $user->organizations()
            ->whereKey($pembayaranKas->organization_id)
            ->wherePivot('role', 'admin')
            ->exists();

        if ($pembayaranKas->user_id !== Auth::id() && ! $isAdmin) {
            abort(403);
        }

        if (! $isAdmin && $pembayaranKas->status === self::STATUS_CONFIRMED) {
            abort(403, 'Tidak dapat menghapus pembayaran yang sudah disetujui.');
        }

        DB::transaction(function () use ($pembayaranKas) {
            if ($pembayaranKas->receipt_path) {
                Storage::disk('public')->delete($pembayaranKas->receipt_path);
            }
            $pembayaranKas->delete();
        });

        return back()->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Download receipt (admin)
     */
    public function downloadReceipt(PembayaranKas $pembayaranKas)
    {
        $org = $pembayaranKas->organization;
        abort_if(! $org, 404, 'Organisasi tidak ditemukan.');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAdmin = $user->organizations()
            ->whereKey($org->id)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_if(! $isAdmin, 403);

        $path = $pembayaranKas->receipt_path;
        abort_if(! $path, 404);

        /** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404);

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = "bukti-pembayaran-{$pembayaranKas->id}.{$ext}";

        return response()->download(
            storage_path('app/public/' . $path),
            $filename
        );
    }


    private function normalizeAmount($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Handle "1.234,56" vs "1,234.56"
        if (strpos($value, ',') !== false && strpos($value, '.') !== false && strrpos($value, ',') > strrpos($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = preg_replace('/[^\d\.]/', '', $value);
        }

        return (float) $value;
    }
}
