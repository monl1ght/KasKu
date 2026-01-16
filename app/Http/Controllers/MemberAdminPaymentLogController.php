<?php

namespace App\Http\Controllers;

use App\Models\PembayaranKas;
use App\Models\PembayaranKasActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberAdminPaymentLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Pastikan user adalah anggota organisasi aktif
        $isMember = $user->organizations()
            ->whereKey($orgId)
            ->exists();

        abort_unless($isMember, 403);

        // tab: pembayaran | pengeluaran
        $tab = (string) $request->query('tab', 'pembayaran');
        if (! in_array($tab, ['pembayaran', 'pengeluaran'], true)) {
            $tab = 'pembayaran';
        }

        $q = trim((string) $request->query('q', ''));

        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : null;

        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : null;

        /**
         * =========================
         * TAB 1: LOG PEMBAYARAN (ADMIN) - HANYA MILIK USER INI
         * =========================
         */
        $paymentLogsBase = PembayaranKasActivityLog::query()
            ->where('organization_id', $orgId)
            ->where('actor_role', 'admin')
            // kunci utama: cuma log yang payer_id = user login (anggota lain tidak tampil)
            ->where('payer_id', $user->id)
            // pastikan cuma transaksi PEMBAYARAN (TYPE_IN)
            ->whereHas('pembayaranKas', function ($tx) {
                $tx->where('type', PembayaranKas::TYPE_IN);
            })
            ->with([
                'actor:id,name,photo',
                'payer:id,name,photo',
                'pembayaranKas:id,organization_id,user_id,bill_id,type,amount,status,description,receipt_path,created_at',
                'pembayaranKas.bill:id,name,amount',
            ]);

        // filter tanggal (untuk log: created_at log)
        if ($from && $to) {
            $paymentLogsBase->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $paymentLogsBase->where('created_at', '>=', $from);
        } elseif ($to) {
            $paymentLogsBase->where('created_at', '<=', $to);
        }

        // search (log pembayaran)
        if ($q !== '') {
            $paymentLogsBase->where(function ($sub) use ($q) {
                $sub->where('action', 'like', "%{$q}%")
                    ->orWhere('old_values', 'like', "%{$q}%")
                    ->orWhere('new_values', 'like', "%{$q}%")
                    ->orWhereHas('actor', fn($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('pembayaranKas', function ($tx) use ($q) {
                        $tx->where('description', 'like', "%{$q}%")
                            ->orWhere('amount', 'like', "%{$q}%")
                            ->orWhereHas('bill', function ($b) use ($q) {
                                $b->where('name', 'like', "%{$q}%");
                            });
                    });
            });
        }

        /**
         * =========================
         * TAB 2: PENGELUARAN ORGANISASI (TRANSAKSI TYPE_OUT)
         * =========================
         * Ini bukan "log pembayaran", tapi data pengeluaran kas organisasi.
         * (Sesuai permintaan: pengeluaran = pengeluaran organisasi)
         */
        $pengeluaranBase = PembayaranKas::query()
            ->where('organization_id', $orgId)
            ->where('type', PembayaranKas::TYPE_OUT)
            ->where('status', PembayaranKas::STATUS_CONFIRMED)
            ->with(['user:id,name,photo']);

        // filter tanggal (untuk transaksi pengeluaran: created_at)
        if ($from && $to) {
            $pengeluaranBase->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $pengeluaranBase->where('created_at', '>=', $from);
        } elseif ($to) {
            $pengeluaranBase->where('created_at', '<=', $to);
        }

        // search (pengeluaran)
        if ($q !== '') {
            $pengeluaranBase->where(function ($sub) use ($q) {
                $sub->where('description', 'like', "%{$q}%")
                    ->orWhere('amount', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        // counts untuk badge tab (ikut filter yang sedang dipakai)
        $countPembayaran  = (clone $paymentLogsBase)->count();
        $countPengeluaran = (clone $pengeluaranBase)->count();

        // data aktif sesuai tab
        $logs = null;
        $pengeluaran = null;

        if ($tab === 'pengeluaran') {
            $pengeluaran = $pengeluaranBase
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString();
        } else {
            $logs = $paymentLogsBase
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString();
        }

        return view('member.admin-payment-log', compact(
            'tab',
            'q',
            'from',
            'to',
            'countPembayaran',
            'countPengeluaran',
            'logs',
            'pengeluaran'
        ));
    }
}
