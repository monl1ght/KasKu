<?php

namespace App\Http\Controllers;

use App\Models\PembayaranKas;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PengeluaranKasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Halaman list pengeluaran kas (khusus bendahara).
     * - Default: tampilkan semua pengeluaran (type=pengeluaran)
     * - Support: search (q), filter status (status), filter tanggal input (from/to via created_at)
     */
    public function index(Request $request)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $org = Organization::findOrFail($orgId);
        $this->assertBendahara($orgId);

        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', '')); // confirmed / rejected / pending / kosong
        $from = $request->query('from'); // YYYY-MM-DD
        $to = $request->query('to');     // YYYY-MM-DD

        $query = PembayaranKas::query()
            ->with('user')
            ->where('organization_id', $org->id)
            ->where('type', PembayaranKas::TYPE_OUT);

        if ($status !== '') {
            $query->where('status', $status);
        }

        // NOTE: sistem kamu saat ini pakai created_at untuk filter periode (dashboard/laporan),
        // jadi di controller ini juga ikut created_at agar konsisten.
        if ($from && $to) {
            $query->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ]);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                    ->orWhere('amount', 'like', "%{$q}%");
            });
        }

        $pengeluaran = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        // Statistik ringkas (buat card summary di view nanti)
        $baseConfirmed = PembayaranKas::query()
            ->where('organization_id', $org->id)
            ->where('status', PembayaranKas::STATUS_CONFIRMED);

        $totalMasukConfirmed = (clone $baseConfirmed)
            ->where('type', PembayaranKas::TYPE_IN)
            ->sum('amount');

        $totalKeluarConfirmed = (clone $baseConfirmed)
            ->where('type', PembayaranKas::TYPE_OUT)
            ->sum('amount');

        $saldoKas = (float) $totalMasukConfirmed - (float) $totalKeluarConfirmed;

        return view('PengeluaranKas.index', [
            'org' => $org,
            'pengeluaran' => $pengeluaran,
            'saldoKas' => $saldoKas,
            'filters' => [
                'q' => $q,
                'status' => $status,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    /**
     * Halaman form create pengeluaran (view dibuat di step berikutnya).
     */
    public function create()
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $org = Organization::findOrFail($orgId);
        $this->assertBendahara($orgId);

        // Sementara kategori hardcode (nanti bisa diambil dari tabel/config)
        $categories = [
            'Operasional',
            'Konsumsi',
            'Transportasi',
            'Perlengkapan',
            'Kegiatan',
            'Lainnya',
        ];

        return view('PengeluaranKas.create', [
            'org' => $org,
            'categories' => $categories,
        ]);
    }

    /**
     * Simpan pengeluaran kas:
     * - type = pengeluaran
     * - status = confirmed (langsung sah, karena bendahara input)
     * - receipt_path wajib (nota/kwitansi)
     * - (opsional) simpan category jika kolom ada; kalau tidak ada, embed ke description
     */
    public function store(Request $request)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $org = Organization::findOrFail($orgId);
        $this->assertBendahara($orgId);

        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required',
            'category' => 'required|string|max:80',
            'description' => 'required|string|max:2000',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        // Normalisasi amount (support format Indonesia: 1.000.000 / 1.234,56)
        $amount = $this->normalizeAmount($data['amount']);
        if ($amount <= 0) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Jumlah pengeluaran harus lebih dari 0.']);
        }

        // Cek saldo (agar tidak minus). Saldo dihitung dari confirmed pemasukan - confirmed pengeluaran.
        $saldoSaatIni = $this->hitungSaldoKasConfirmed($org->id);
        if ($amount > $saldoSaatIni) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Saldo kas tidak mencukupi untuk melakukan pengeluaran ini.']);
        }

        $storedPath = null;

        DB::beginTransaction();
        try {
            // Upload bukti
            if ($request->hasFile('receipt')) {
                $storedPath = $request->file('receipt')->store('receipts', 'public');
            }

            $payload = [
                'organization_id' => $org->id,
                'user_id' => Auth::id(),
                'bill_id' => Schema::hasColumn('pembayaran_kas', 'bill_id') ? null : null,

                'type' => PembayaranKas::TYPE_OUT,
                'status' => PembayaranKas::STATUS_CONFIRMED,
                'amount' => number_format($amount, 2, '.', ''),

                // default: simpan keterangan + sisipkan kategori jika tidak ada kolom category
                'description' => $this->buildDescriptionWithCategory(
                    (string) $data['category'],
                    (string) $data['description']
                ),
            ];

            // payment_date (kalau kolomnya ada)
            if (Schema::hasColumn('pembayaran_kas', 'payment_date')) {
                $payload['payment_date'] = $data['payment_date'];
            }

            // receipt_path (kalau kolomnya ada)
            if ($storedPath && Schema::hasColumn('pembayaran_kas', 'receipt_path')) {
                $payload['receipt_path'] = $storedPath;
            }

            // category (kalau ada kolom khusus)
            if (Schema::hasColumn('pembayaran_kas', 'category')) {
                $payload['category'] = $data['category'];

                // kalau ada kolom category, description tidak perlu embed kategori.
                $payload['description'] = $data['description'];
            }

            // verified_by & verified_at (kalau ada)
            if (Schema::hasColumn('pembayaran_kas', 'verified_by')) {
                $payload['verified_by'] = Auth::id();
            }
            if (Schema::hasColumn('pembayaran_kas', 'verified_at')) {
                $payload['verified_at'] = now();
            }

            PembayaranKas::create($payload);

            DB::commit();

            return redirect()
                ->route('pengeluaran-kas.index')
                ->with('success', 'Pengeluaran kas berhasil dicatat dan saldo otomatis berkurang.');
        } catch (Throwable $e) {
            DB::rollBack();

            // kalau upload sudah terjadi tapi DB gagal, hapus file biar tidak jadi sampah
            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal mencatat pengeluaran kas: ' . $e->getMessage());
        }
    }

    /**
     * Preview bukti nota/kwitansi (inline) – mengikuti gaya VerificationController::showReceipt().
     */
    public function showReceipt(PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        abort_if((int) $pembayaranKas->organization_id !== (int) $orgId, 403);

        $this->assertBendahara($orgId);

        // hanya untuk transaksi pengeluaran
        abort_if($pembayaranKas->type !== PembayaranKas::TYPE_OUT, 404);

        $path = $pembayaranKas->receipt_path;
        abort_if(! $path, 404);

        // normalisasi path (hindari dobel prefix)
        $path = ltrim($path, '/');
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        abort_if(! Storage::disk('public')->exists($path), 404);

        $fullPath = Storage::disk('public')->path($path);
        $mime = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';
        $fileName = basename($path);

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Hapus transaksi pengeluaran (opsional) – hanya bendahara & hanya pengeluaran.
     * Catatan: karena saldo dihitung dari transaksi, menghapus record akan otomatis “mengembalikan” saldo.
     */
    public function destroy(PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        abort_if((int) $pembayaranKas->organization_id !== (int) $orgId, 403);

        $this->assertBendahara($orgId);

        abort_if($pembayaranKas->type !== PembayaranKas::TYPE_OUT, 404);

        DB::beginTransaction();
        try {
            // simpan path untuk delete file setelah delete record
            $path = $pembayaranKas->receipt_path;

            $pembayaranKas->delete();

            // hapus file bukti jika ada
            if ($path) {
                $path = ltrim($path, '/');
                $path = preg_replace('#^public/#', '', $path);
                $path = preg_replace('#^storage/#', '', $path);

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            DB::commit();

            return redirect()
                ->route('pengeluaran-kas.index')
                ->with('success', 'Pengeluaran kas berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus pengeluaran kas: ' . $e->getMessage());
        }
    }

    /* =========================
     * Helpers
     * ========================= */

    private function assertBendahara(int|string $orgId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAdmin = $user->organizations()
            ->whereKey($orgId)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_if(! $isAdmin, 403);
    }

    private function normalizeAmount(mixed $raw): float
    {
        $s = trim((string) $raw);
        $s = str_replace(' ', '', $s);

        // buang karakter non digit kecuali . dan ,
        $s = preg_replace('/[^\d\.,]/', '', $s) ?? '';

        $hasDot = str_contains($s, '.');
        $hasComma = str_contains($s, ',');

        if ($hasDot && $hasComma) {
            // asumsi format ID: 1.234,56
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif ($hasComma && ! $hasDot) {
            // asumsi decimal koma: 1234,56
            $s = str_replace(',', '.', $s);
        }

        // kalau masih ada lebih dari 1 titik, ambil yang terakhir sebagai desimal
        if (substr_count($s, '.') > 1) {
            $parts = explode('.', $s);
            $decimal = array_pop($parts);
            $s = implode('', $parts) . '.' . $decimal;
        }

        return (float) $s;
    }

    private function hitungSaldoKasConfirmed(int $orgId): float
    {
        $base = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED);

        $masuk = (float) (clone $base)->where('type', PembayaranKas::TYPE_IN)->sum('amount');
        $keluar = (float) (clone $base)->where('type', PembayaranKas::TYPE_OUT)->sum('amount');

        return $masuk - $keluar;
    }

    private function buildDescriptionWithCategory(string $category, string $description): string
    {
        $category = trim($category);
        $description = trim($description);

        // Format yang gampang dibaca & bisa diparsing belakangan
        return "[Kategori: {$category}] " . $description;
    }

    /**
     * Halaman form edit pengeluaran.
     */
    public function edit(PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        abort_if((int) $pembayaranKas->organization_id !== (int) $orgId, 403);
        $this->assertBendahara($orgId);
        abort_if($pembayaranKas->type !== PembayaranKas::TYPE_OUT, 404);

        $org = Organization::findOrFail($orgId);

        $categories = ['Operasional', 'Konsumsi', 'Transportasi', 'Perlengkapan', 'Kegiatan', 'Lainnya'];

        // (opsional) kalau kamu memang sudah punya helper extract
        $category = method_exists($this, 'extractCategory') ? $this->extractCategory($pembayaranKas) : null;
        $description = method_exists($this, 'extractCleanDescription') ? $this->extractCleanDescription($pembayaranKas) : null;

        return view('PengeluaranKas.edit', [
            'org' => $org,
            'pembayaranKas' => $pembayaranKas,   // ✅ INI WAJIB ADA
            'categories' => $categories,
            'category' => $category,
            'description' => $description,
        ]);
    }


    /**
     * Update pengeluaran:
     * - tetap type = pengeluaran
     * - tetap status = confirmed
     * - receipt optional (kalau upload baru, replace file lama)
     * - cek saldo: saldoSaatIni + amountLama harus >= amountBaru
     */
    public function update(Request $request, PembayaranKas $pembayaranKas)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        abort_if((int) $pembayaranKas->organization_id !== (int) $orgId, 403);

        $org = Organization::findOrFail($orgId);
        $this->assertBendahara($orgId);

        abort_if($pembayaranKas->type !== PembayaranKas::TYPE_OUT, 404);

        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required',
            'category' => 'required|string|max:80',
            'description' => 'required|string|max:2000',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        $newAmount = $this->normalizeAmount($data['amount']);
        if ($newAmount <= 0) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Jumlah pengeluaran harus lebih dari 0.']);
        }

        // saldo check: saat edit, amount lama dianggap "dikembalikan" dulu
        $saldoSaatIni = $this->hitungSaldoKasConfirmed($org->id);
        $saldoEfektif = $saldoSaatIni + (float) $pembayaranKas->amount;

        if ($newAmount > $saldoEfektif) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Saldo kas tidak mencukupi untuk melakukan perubahan pengeluaran ini.']);
        }

        $newStoredPath = null;
        $oldPath = $pembayaranKas->receipt_path;

        DB::beginTransaction();
        try {
            // kalau ada upload baru
            if ($request->hasFile('receipt')) {
                $newStoredPath = $request->file('receipt')->store('receipts', 'public');
            }

            $payload = [
                // pastikan tidak bisa diubah jadi selain pengeluaran/confirmed
                'type' => PembayaranKas::TYPE_OUT,
                'status' => PembayaranKas::STATUS_CONFIRMED,

                'amount' => number_format($newAmount, 2, '.', ''),
            ];

            // payment_date (kalau ada kolomnya)
            if (Schema::hasColumn('pembayaran_kas', 'payment_date')) {
                $payload['payment_date'] = $data['payment_date'];
            }

            // receipt_path (kalau ada kolomnya & upload baru)
            if ($newStoredPath && Schema::hasColumn('pembayaran_kas', 'receipt_path')) {
                $payload['receipt_path'] = $newStoredPath;
            }

            // category (kalau ada kolom khusus)
            if (Schema::hasColumn('pembayaran_kas', 'category')) {
                $payload['category'] = $data['category'];
                $payload['description'] = $data['description'];
            } else {
                // embed kategori ke description
                $payload['description'] = $this->buildDescriptionWithCategory(
                    (string) $data['category'],
                    (string) $data['description']
                );
            }

            // verified_by & verified_at (kalau ada)
            if (Schema::hasColumn('pembayaran_kas', 'verified_by')) {
                $payload['verified_by'] = Auth::id();
            }
            if (Schema::hasColumn('pembayaran_kas', 'verified_at')) {
                $payload['verified_at'] = now();
            }

            $pembayaranKas->update($payload);

            DB::commit();

            // hapus file lama jika berhasil update dan ada file baru
            if ($newStoredPath && $oldPath) {
                $p = ltrim($oldPath, '/');
                $p = preg_replace('#^public/#', '', $p);
                $p = preg_replace('#^storage/#', '', $p);

                if (Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            return redirect()
                ->route('pengeluaran-kas.index')
                ->with('success', 'Pengeluaran kas berhasil diperbarui.');
        } catch (Throwable $e) {
            DB::rollBack();

            // kalau upload baru sudah terjadi tapi update gagal, hapus file baru
            if ($newStoredPath && Storage::disk('public')->exists($newStoredPath)) {
                Storage::disk('public')->delete($newStoredPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pengeluaran kas: ' . $e->getMessage());
        }
    }

    /**
     * Ambil kategori untuk form edit:
     * - kalau kolom category ada, pakai itu
     * - kalau tidak, parse dari prefix [Kategori: ...]
     */
    private function extractCategory(PembayaranKas $row): string
    {
        if (isset($row->category) && $row->category) {
            return (string) $row->category;
        }

        $desc = (string) ($row->description ?? '');
        if (preg_match('/^\[Kategori:\s*(.*?)\]\s*/', $desc, $m)) {
            return trim($m[1]);
        }

        return 'Lainnya';
    }

    /**
     * Ambil description yang bersih (tanpa prefix kategori) untuk form edit.
     */
    private function extractCleanDescription(PembayaranKas $row): string
    {
        $desc = (string) ($row->description ?? '');
        $desc = preg_replace('/^\[Kategori:\s*(.*?)\]\s*/', '', $desc);
        $desc = trim($desc);

        return $desc !== '' ? $desc : '-';
    }
}
