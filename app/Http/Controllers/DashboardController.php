<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\JoinRequest;
use App\Models\PembayaranKas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Pastikan user anggota organisasi + ambil role dari pivot
        $activeOrganization = $user->organizations()
            ->where('organizations.id', $orgId)
            ->first();

        if (! $activeOrganization) {
            abort(403, 'Anda tidak memiliki akses ke organisasi ini.');
        }

        $activeRole = $activeOrganization->pivot->role ?? 'member';

        // Dashboard bendahara hanya admin
        if ($activeRole !== 'admin') {
            return redirect()->route('member.dashboard');
        }

        /* =========================================================
         * 0) FILTER RANGE TANGGAL (untuk chart & saldo periode)
         * - query param:
         *   - range: 6m | ytd | 30d | custom (opsional)
         *   - from: YYYY-MM-DD
         *   - to:   YYYY-MM-DD
         * Default: 6 bulan terakhir
         * ========================================================= */
        $range = (string) $request->query('range', '6m');

        $from = $request->filled('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : null;

        $to = $request->filled('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : null;

        // Preset kalau from/to tidak ada, atau range bukan custom
        if ($range !== 'custom' && (! $from || ! $to)) {
            if ($range === 'ytd') {
                $from = now()->startOfYear();
                $to   = now()->endOfDay();
            } elseif ($range === '30d') {
                $from = now()->subDays(30)->startOfDay();
                $to   = now()->endOfDay();
            } else {
                // default 6m
                $from = now()->subMonths(5)->startOfMonth(); // total 6 bulan termasuk bulan ini
                $to   = now()->endOfDay();
            }
        }

        // Kalau tetap kosong (misal input invalid), fallback aman
        if (! $from || ! $to) {
            $from = now()->subMonths(5)->startOfMonth();
            $to   = now()->endOfDay();
            $range = '6m';
        }

        // Safety: kalau kebalik
        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        // Batasi range biar tidak kebangetan (opsional, tapi aman)
        // Misal max 24 bulan:
        if ($from->diffInMonths($to) > 24) {
            $from = $to->copy()->subMonths(24)->startOfDay();
            $range = 'custom';
        }

        /* =========================
         * 1) PENDING VERIFIKASI
         * ========================= */
        $pendingTodayCount = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_PENDING)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $pendingCount = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_PENDING)
            ->count();

        /* =========================================================
         * 2) SALDO KAS (PERIODE FILTER)
         * confirmed pemasukan - confirmed pengeluaran dalam range
         * + growth dibanding periode sebelumnya (durasi sama)
         * ========================================================= */
        $periodIncome = (float) PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->where('type', PembayaranKas::TYPE_IN)
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $periodExpense = (float) PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->where('type', PembayaranKas::TYPE_OUT)
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $saldoKas = $periodIncome - $periodExpense;

        // Growth: bandingkan periode sebelumnya dengan panjang hari yang sama
        $days = max(1, $from->diffInDays($to) + 1);
        $prevTo = $from->copy()->subDay()->endOfDay();
        $prevFrom = $prevTo->copy()->subDays($days - 1)->startOfDay();

        $saldoPrevPeriod = (float) PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = ? THEN amount ELSE 0 END),0) -
                COALESCE(SUM(CASE WHEN type = ? THEN amount ELSE 0 END),0) AS saldo
            ", [PembayaranKas::TYPE_IN, PembayaranKas::TYPE_OUT])
            ->value('saldo') ?? 0;

        $saldoGrowthPercent = 0.0;
        if (abs($saldoPrevPeriod) > 0.000001) {
            $saldoGrowthPercent = round((($saldoKas - $saldoPrevPeriod) / $saldoPrevPeriod) * 100, 1);
        } elseif (abs($saldoKas) > 0.000001) {
            $saldoGrowthPercent = 100.0;
        }

        /* =========================
         * 3) ANGGOTA AKTIF + BARU MINGGU INI
         * ========================= */
        $anggotaAktifCount = (int) $activeOrganization->users()
            ->where('users.status', 'aktif')
            ->count();

        $anggotaBaruMingguIniCount = (int) $activeOrganization->users()
            ->where('users.status', 'aktif')
            ->wherePivot('created_at', '>=', now()->startOfWeek())
            ->count();

        /* =========================
         * 4) TUNGGAKAN (sesuai definisi kamu)
         * LUNAS hanya jika ada pembayaran CONFIRMED.
         * Pending tetap tunggakan.
         * Semua bill org wajib dibayar semua anggota aktif.
         * (Tunggakan biasanya ALL-TIME berdasarkan bill yang ada)
         * ========================= */
        $bills = Bill::query()
            ->where('organization_id', $orgId)
            ->get(['id', 'amount']);

        $totalBillsCount = $bills->count();

        $activeMemberIds = $activeOrganization->users()
            ->where('users.status', 'aktif')
            ->pluck('users.id');

        $paidBillsPerUser = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->whereNotNull('bill_id')
            ->whereIn('user_id', $activeMemberIds)
            ->select('user_id', DB::raw('COUNT(DISTINCT bill_id) as paid_cnt'))
            ->groupBy('user_id')
            ->pluck('paid_cnt', 'user_id');

        $tunggakanMembersCount = 0;
        if ($totalBillsCount > 0) {
            foreach ($activeMemberIds as $uid) {
                $paidCnt = (int) ($paidBillsPerUser[$uid] ?? 0);
                if ($paidCnt < $totalBillsCount) {
                    $tunggakanMembersCount++;
                }
            }
        }

        $paidCountPerBill = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->whereNotNull('bill_id')
            ->whereIn('user_id', $activeMemberIds)
            ->select('bill_id', DB::raw('COUNT(DISTINCT user_id) as paid_users'))
            ->groupBy('bill_id')
            ->pluck('paid_users', 'bill_id');

        $totalTunggakan = 0.0;
        $activeMembersCount = (int) $activeMemberIds->count();

        if ($totalBillsCount > 0 && $activeMembersCount > 0) {
            foreach ($bills as $bill) {
                $paidUsers = (int) ($paidCountPerBill[$bill->id] ?? 0);
                $unpaidUsers = max(0, $activeMembersCount - $paidUsers);
                $totalTunggakan += $unpaidUsers * (float) $bill->amount;
            }
        }

        /* =========================================================
         * 5) CHART PEMASUKAN (sesuai range)
         * confirmed + type pemasukan, group by bulan created_at
         * ========================================================= */
        $startMonth = $from->copy()->startOfMonth();
        $endMonth   = $to->copy()->startOfMonth();

        $months = collect();
        $cursor = $startMonth->copy();
        while ($cursor->lte($endMonth)) {
            $months->push([
                'key'   => $cursor->format('Y-m'),
                'label' => $cursor->translatedFormat('M'),
            ]);
            $cursor->addMonth();
        }

        $incomeByMonth = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->where('type', PembayaranKas::TYPE_IN)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $incomeChartLabels = $months->pluck('label')->toArray();
        $incomeChartData   = $months->map(fn($m) => (float) ($incomeByMonth[$m['key']] ?? 0))->toArray();

        /* =========================================================
         * 6) AKTIVITAS TERBARU (fix: sort by time)
         * ========================================================= */
        $paymentActivities = PembayaranKas::query()
            ->with(['user:id,name', 'bill:id,name'])
            ->where('organization_id', $orgId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($t) {
                $status = $t->status;

                $type = match ($status) {
                    PembayaranKas::STATUS_CONFIRMED => 'payment_confirmed',
                    PembayaranKas::STATUS_REJECTED  => 'payment_rejected',
                    default                         => 'payment_pending',
                };

                $title = match ($status) {
                    PembayaranKas::STATUS_CONFIRMED => 'Pembayaran Diterima',
                    PembayaranKas::STATUS_REJECTED  => 'Pembayaran Ditolak',
                    default                         => 'Menunggu Verifikasi',
                };

                $subtitle = ($t->user?->name ?? 'Anggota')
                    . ' membayar '
                    . ($t->bill?->name ?? 'Pembayaran Kas');

                $time = $t->verified_at ?? $t->created_at;

                return [
                    'type'       => $type,
                    'title'      => $title,
                    'subtitle'   => $subtitle,
                    'time_human' => optional($time)->diffForHumans(),
                    'time_sort'  => optional($time)->timestamp ?? 0,
                ];
            });

        $joinActivities = JoinRequest::query()
            ->with('user:id,name')
            ->where('organization_id', $orgId)
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($jr) {
                return [
                    'type'       => 'join_pending',
                    'title'      => 'Permintaan Bergabung',
                    'subtitle'   => ($jr->user?->name ?? 'Calon anggota') . ' meminta bergabung via kode',
                    'time_human' => optional($jr->created_at)->diffForHumans(),
                    'time_sort'  => optional($jr->created_at)->timestamp ?? 0,
                ];
            });

        $recentActivities = $paymentActivities
            ->concat($joinActivities)
            ->sortByDesc('time_sort')
            ->take(5)
            ->values()
            ->map(function ($a) {
                unset($a['time_sort']);
                return $a;
            })
            ->toArray();

        return view('Dashboard', [
            'activeOrganization'        => $activeOrganization,
            'activeRole'                => $activeRole,

            'pendingTodayCount'         => $pendingTodayCount,
            'pendingCount'              => $pendingCount,

            // saldo periode
            'saldoKas'                  => $saldoKas,
            'saldoGrowthPercent'        => $saldoGrowthPercent,

            // tunggakan all-time sesuai definisi kamu
            'totalTunggakan'            => $totalTunggakan,
            'tunggakanMembersCount'     => $tunggakanMembersCount,

            'anggotaAktifCount'         => $anggotaAktifCount,
            'anggotaBaruMingguIniCount' => $anggotaBaruMingguIniCount,

            'incomeChartLabels'         => $incomeChartLabels,
            'incomeChartData'           => $incomeChartData,

            'recentActivities'          => $recentActivities,

            // kirim balik filter untuk isi input date di blade
            'from'                      => $from->toDateString(),
            'to'                        => $to->toDateString(),
            'range'                     => $range,
        ]);
    }
}
