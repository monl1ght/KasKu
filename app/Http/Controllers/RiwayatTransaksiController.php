<?php

namespace App\Http\Controllers;

use App\Models\PembayaranKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatTransaksiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $orgId  = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $transactions = PembayaranKas::query()
            ->with([
                'bill:id,name',
                'verifier:id,name'
            ])
            ->where('organization_id', $orgId)
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => 'TRX-' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
                    'charge_name' => $t->bill?->name ?? 'Pembayaran Kas',
                    'category' => $t->bill ? 'Wajib' : 'Sukarela',
                    'amount' => (float) $t->amount,

                    // kamu pakai created_at sebagai payment_date di UI (OK)
                    'payment_date' => optional($t->created_at)->toIso8601String(),
                    'verification_date' => optional($t->verified_at)->toIso8601String(),

                    'status' => match ($t->status) {
                        'confirmed' => 'verified',
                        'pending'   => 'pending',
                        'rejected'  => 'rejected',
                        default     => 'pending',
                    },

                    'proof_image' => $t->receipt_path
                        ? asset('storage/' . $t->receipt_path)
                        : null,

                    'notes' => $t->description,
                    'verified_by' => $t->verifier?->name,
                    'rejection_reason' => $t->rejection_reason,
                ];
            });

        return view('member.RiwayatTransaksi', compact('transactions'));
    }

    /**
     * Export PDF (ikut filter: status, q, from, to)
     * Query params:
     * - status: all|verified|pending|rejected
     * - q: string
     * - from: YYYY-MM-DD
     * - to: YYYY-MM-DD
     */
    public function exportPdf(Request $request)
    {
        $userId = Auth::id();
        $orgId  = session('active_organization_id');

        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $status = $request->string('status', 'all')->toString();
        $q      = trim((string) $request->get('q', ''));
        $from   = $request->get('from');
        $to     = $request->get('to');

        $query = PembayaranKas::query()
            ->with(['bill:id,name', 'verifier:id,name'])
            ->where('organization_id', $orgId)
            ->where('user_id', $userId);

        // Filter status (frontend: verified/pending/rejected)
        if ($status !== 'all') {
            $dbStatus = match ($status) {
                'verified' => PembayaranKas::STATUS_CONFIRMED,
                'pending'  => PembayaranKas::STATUS_PENDING,
                'rejected' => PembayaranKas::STATUS_REJECTED,
                default    => null,
            };
            if ($dbStatus) {
                $query->where('status', $dbStatus);
            }
        }

        // Filter tanggal (pakai created_at agar konsisten dengan UI kamu)
        // NOTE: scopeBetweenDate di model sebenarnya pakai created_at juga (walau komentarnya payment_date)
        if ($from || $to) {
            $query->betweenDate($from, $to);
        }

        // Search (id TRX-0001 / numeric id / bill name / description)
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $qq = strtolower($q);

                // Kalau user cari "TRX-0001" -> ambil angkanya
                if (preg_match('/trx\D*(\d+)/i', $q, $m)) {
                    $w->orWhere('id', (int) $m[1]);
                } elseif (ctype_digit($q)) {
                    $w->orWhere('id', (int) $q);
                }

                $w->orWhereHas('bill', function ($b) use ($q) {
                    $b->where('name', 'like', '%' . $q . '%');
                });

                $w->orWhere('description', 'like', '%' . $q . '%');
                $w->orWhere('type', 'like', '%' . $q . '%');
                $w->orWhere('status', 'like', '%' . $q . '%');
            });
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                // DomPDF lebih aman pakai path lokal (bukan URL) untuk gambar
                $localProofPath = $t->receipt_path
                    ? public_path('storage/' . $t->receipt_path)
                    : null;

                return [
                    'trx_code' => 'TRX-' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
                    'charge_name' => $t->bill?->name ?? 'Pembayaran Kas',
                    'category' => $t->bill ? 'Wajib' : 'Sukarela',
                    'amount' => (float) $t->amount,
                    'created_at' => $t->created_at,
                    'verified_at' => $t->verified_at,
                    'status_ui' => match ($t->status) {
                        PembayaranKas::STATUS_CONFIRMED => 'verified',
                        PembayaranKas::STATUS_PENDING => 'pending',
                        PembayaranKas::STATUS_REJECTED => 'rejected',
                        default => 'pending',
                    },
                    'verified_by' => $t->verifier?->name,
                    'rejection_reason' => $t->rejection_reason,
                    'notes' => $t->description,
                    'proof_local_path' => $localProofPath,
                ];
            });

        $meta = [
            'generated_at' => now(),
            'filters' => [
                'status' => $status,
                'q' => $q,
                'from' => $from,
                'to' => $to,
            ],
        ];

        $pdf = Pdf::loadView('member.pdf.riwayat_transaksi', [
            'rows' => $rows,
            'meta' => $meta,
        ])->setPaper('A4', 'landscape');

        $filename = 'riwayat-transaksi-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }
}
