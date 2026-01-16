<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\PembayaranKas;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // di bagian use atas

class LaporanRekapitulasiController extends Controller
{
    public function index(Request $request)
    {
        /* ===============================
         * ORGANIZATION CONTEXT
         * =============================== */
        $orgId = session('active_organization_id')
            ?? optional($request->user())->organization_id;

        /* ===============================
         * DATE RANGE (AMAN)
         * =============================== */
        $minDate = PembayaranKas::when(
            $orgId,
            fn($q) =>
            $q->where('organization_id', $orgId)
        )->min('created_at');

        $maxDate = PembayaranKas::when(
            $orgId,
            fn($q) =>
            $q->where('organization_id', $orgId)
        )->max('created_at');

        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : ($minDate ? Carbon::parse($minDate)->startOfDay() : now()->startOfMonth());

        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : ($maxDate ? Carbon::parse($maxDate)->endOfDay() : now()->endOfDay());

        $q = $request->query('q');

        /* ===============================
         * SUMMARY KAS
         * =============================== */
        $totalPemasukan = PembayaranKas::query()
            ->when($orgId, fn($q) => $q->where('organization_id', $orgId))
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->where('type', PembayaranKas::TYPE_IN)
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $totalPengeluaran = PembayaranKas::query()
            ->when($orgId, fn($q) => $q->where('organization_id', $orgId))
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->where('type', PembayaranKas::TYPE_OUT)
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $saldoKas = $totalPemasukan - $totalPengeluaran;

        /* ===============================
 * LOGIKA TUNGGAKAN (FULL REAL)
 * =============================== */

        /* ===============================
 * LOGIKA TUNGGAKAN (FULL REAL) + FOTO (OPTIMIZED)
 * =============================== */

        $bills = Bill::where('organization_id', $orgId)->get();

        $billMenunggak  = collect();
        $totalTunggakan = 0;

        if ($bills->isNotEmpty()) {

            // Semua user yang pernah tercatat (hindari null)
            $anggotaOrganisasi = PembayaranKas::where('organization_id', $orgId)
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id')
                ->values();

            if ($anggotaOrganisasi->isNotEmpty()) {

                // Ambil data user SEKALI (biar ga N+1)
                $usersById = User::query()
                    ->whereIn('id', $anggotaOrganisasi)
                    ->select('id', 'name', 'nim', 'photo')
                    ->get()
                    ->keyBy('id');

                // Ambil daftar bill_id yang sudah dibayar (confirmed) per user SEKALI
                $paidMap = PembayaranKas::query()
                    ->where('organization_id', $orgId)
                    ->where('status', PembayaranKas::STATUS_CONFIRMED)
                    ->whereNotNull('user_id')
                    ->whereNotNull('bill_id')
                    ->whereIn('user_id', $anggotaOrganisasi)
                    ->get(['user_id', 'bill_id'])
                    ->groupBy('user_id')
                    ->map(fn($rows) => $rows->pluck('bill_id')->unique()->values());

                foreach ($anggotaOrganisasi as $userId) {

                    $user = $usersById->get($userId);
                    if (!$user) continue;

                    $paidBillIds = $paidMap->get($userId, collect());

                    // Bill yang BELUM dibayar user ini
                    $billBelumDibayar = $bills->filter(fn($bill) => !$paidBillIds->contains($bill->id));

                    if ($billBelumDibayar->isEmpty()) continue;

                    $jumlahTagihan = $billBelumDibayar->count();
                    $totalNominal  = $billBelumDibayar->sum('amount');

                    $billMenunggak->push((object) [
                        'id'             => $user->id,
                        'name'           => $user->name,
                        'nim'            => $user->nim,
                        'photo'          => $user->photo, // ✅ INI YANG KAMU BUTUH
                        'total_amount'   => $totalNominal,
                        'jumlah_tagihan' => $jumlahTagihan,
                    ]);

                    $totalTunggakan += $totalNominal;
                }
            }
        }


        /* ===============================
         * RIWAYAT TRANSAKSI
         * =============================== */
        $transactions = PembayaranKas::query()
            ->with(['user:id,name,nim,photo', 'bill']) // <-- hapus period di sini
            ->when($orgId, fn($q) => $q->where('organization_id', $orgId))
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('description', 'like', "%{$q}%")
                        ->orWhere('amount', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%")
                            ->orWhere('nim', 'like', "%{$q}%"))
                        ->orWhereHas('bill', fn($b) => $b->where('name', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        /* ===============================
         * RETURN VIEW
         * =============================== */
        return view('Laporan&Rekapitulasi', [
            'totalPemasukan'   => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoKas'         => $saldoKas,

            'totalTunggakan'   => $totalTunggakan,
            'billMenunggak'    => $billMenunggak,

            'transactions'     => $transactions,

            'from'             => $from,
            'to'               => $to,
            'q'                => $q,
        ]);
    }
    public function detailTunggakan(User $user)
    {
        $orgId = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        // Semua bill organisasi
        $bills = Bill::where('organization_id', $orgId)->get();

        // Bill yang belum dibayar user ini
        $billBelumDibayar = $bills->filter(function ($bill) use ($user) {
            return !PembayaranKas::where('bill_id', $bill->id)
                ->where('user_id', $user->id)
                ->where('status', PembayaranKas::STATUS_CONFIRMED)
                ->exists();
        })->map(function ($bill) {

            // Nama tagihan
            $namaTagihan = $bill->name
                ?? $bill->title
                ?? 'Tagihan Kas';

            // PERIODE = TANGGAL LENGKAP (hari-bulan-tahun)
            if (!empty($bill->period)) {
                // kalau period berupa date / datetime
                $periode = Carbon::parse($bill->period)->translatedFormat('d M Y');
            } else {
                // fallback paling aman
                $periode = $bill->created_at->translatedFormat('d M Y');
            }

            return (object) [
                'id'      => $bill->id,
                'nama'    => $namaTagihan,
                'periode' => $periode,   // ← tetap bernama "periode"
                'amount'  => $bill->amount,
            ];
        });

        $totalTunggakan = $billBelumDibayar->sum('amount');

        return view('laporan.tunggakan-detail', [
            'user'           => $user,
            'bills'          => $billBelumDibayar,
            'totalTunggakan' => $totalTunggakan,
        ]);
    }
    public function showReceipt(PembayaranKas $pembayaranKas)
    {
        if (!$pembayaranKas->receipt_path) {
            abort(404);
        }

        $path = $pembayaranKas->receipt_path;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('public')->path($path),
            ['Content-Type' => mime_content_type(Storage::disk('public')->path($path))]
        );
    }

    public function exportPdf(Request $request)
    {
        // WAJIB ambil dari session organisasi aktif (konsisten dengan halaman index kamu)
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        // parse filter
        $fromRaw = $request->query('from');
        $toRaw   = $request->query('to');
        $q       = trim((string) $request->query('q', ''));

        $from = $fromRaw ? Carbon::parse($fromRaw)->startOfDay() : null;
        $to   = $toRaw   ? Carbon::parse($toRaw)->endOfDay() : null;

        /**
         * BASE QUERY (KUNCI)
         * Semua data PDF + summary harus dari base yang sama:
         * - org sama
         * - status confirmed
         * - filter tanggal sama
         * - filter search sama
         */
        $base = PembayaranKas::query()
            ->with([
                'user:id,name,nim',
                // kalau kamu butuh nama tagihan pemasukan dari relasi bill, aktifkan ini:
                // 'bill:id,title,period',
            ])
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED);

        // filter tanggal (support from saja / to saja)
        if ($from && $to) {
            $base->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $base->where('created_at', '>=', $from);
        } elseif ($to) {
            $base->where('created_at', '<=', $to);
        }

        // filter search
        if ($q !== '') {
            $base->where(function ($sub) use ($q) {
                $sub->where('description', 'like', "%{$q}%")
                    ->orWhere('amount', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                            ->orWhere('nim', 'like', "%{$q}%");
                    });
            });
        }

        // ambil semua transaksi untuk PDF
        $transactions = (clone $base)
            ->orderByDesc('created_at')
            ->get();

        // summary (pasti konsisten karena clone base yang sama)
        $totalPemasukan = (float) (clone $base)
            ->where('type', PembayaranKas::TYPE_IN)
            ->sum('amount');

        $totalPengeluaran = (float) (clone $base)
            ->where('type', PembayaranKas::TYPE_OUT)
            ->sum('amount');

        $saldoKas = $totalPemasukan - $totalPengeluaran;

        // count total per jenis (buat badge/tab agar tidak berubah-ubah)
        $countMasukAll = (int) (clone $base)->where('type', PembayaranKas::TYPE_IN)->count();
        $countKeluarAll = (int) (clone $base)->where('type', PembayaranKas::TYPE_OUT)->count();

        $data = [
            'transactions'     => $transactions,
            'totalPemasukan'   => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoKas'         => $saldoKas,
            'countMasukAll'    => $countMasukAll,
            'countKeluarAll'   => $countKeluarAll,
            'from'             => $fromRaw,
            'to'               => $toRaw,
            'q'                => $q,
            'printedAt'        => now(),
        ];

        $pdf = Pdf::loadView('laporan.rekapitulasi-pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('rekapitulasi-kas.pdf');
    }
}
