<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PembayaranKas;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        /**
         * =====================================
         * 🔥 FIX UTAMA: PASTIKAN ORGANISASI AKTIF
         * =====================================
         */
        $orgId = session('active_organization_id');

        if (! $orgId) {
            $organization = $user->organizations()->first();

            if (! $organization) {
                return redirect()->route('organization.select')
                    ->with('warning', 'Anda belum tergabung dalam organisasi.');
            }

            session(['active_organization_id' => $organization->id]);
            $orgId = $organization->id;
        }

        /**
         * ============================
         * TOTAL TAGIHAN (PER MEMBER)
         * ============================
         * Semua bill organisasi WAJIB dibayar oleh member
         */
        $totalCharges = (int) Bill::where('organization_id', $orgId)
            ->sum('amount');

        /**
         * ===========================================
         * TOTAL DIBAYAR (HANYA MILIK USER & CONFIRMED)
         * ===========================================
         */
        $totalPayments = (int) PembayaranKas::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');

        /**
         * ============================
         * PERHITUNGAN TURUNAN
         * ============================
         */
        $paid = $totalPayments;
        $remaining = max(0, $totalCharges - $paid);

        $percent = $totalCharges > 0
            ? min(100, round(($paid / $totalCharges) * 100))
            : 0;

        /**
         * ============================
         * AKTIVITAS TERAKHIR
         * ============================
         */
        $recentActivities = PembayaranKas::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'type' => match ($p->status) {
                        'confirmed' => 'payment',
                        'rejected'  => 'rejected',
                        default     => 'pending',
                    },
                    'title' => 'Pembayaran: Rp ' . number_format($p->amount, 0, ',', '.'),
                    'subtitle' => match ($p->status) {
                        'confirmed' => 'Pembayaran kas terverifikasi',
                        'rejected'  => 'Ditolak oleh bendahara',
                        default     => 'Menunggu verifikasi',
                    },
                    'time_iso' => optional($p->created_at)->toIsoString(),
                    'time_human' => optional($p->created_at)->diffForHumans(),
                ];
            })
            ->toArray();

        /**
         * ============================
         * KIRIM KE VIEW
         * ============================
         */
        return view('member.dashboard', [
            'total'             => $totalCharges,    // <-- kirim sebagai $total (dipakai view)
            'totalPayments'     => $totalPayments,   // (opsional, kalau butuh)
            'paid'             => $paid,
            'remaining'        => $remaining,
            'remainingDue'     => $remaining,
            'percent'          => $percent,
            'recentActivities' => $recentActivities,
        ]);
    }
}
