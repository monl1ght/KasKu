<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Kas</title>
    <style>
        /* PAGE */
        @page {
            margin: 24px 24px 40px 24px;
        }

        /* Reset & Base Styles */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Utility */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #666; }
        .mb-2 { margin-bottom: 1rem; }
        .mt-2 { margin-top: 1rem; }
        .section-title {
            margin: 14px 0 8px 0;
            color: #2c3e50;
            font-size: 13px;
            font-weight: bold;
        }
        .section-note {
            font-size: 10px;
            color: #666;
            margin: 0 0 8px 0;
        }

        /* Header Section */
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header h2 {
            margin: 0 0 4px 0;
            color: #2c3e50;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header .meta {
            font-size: 11px;
            color: #555;
        }

        /* Summary Box */
        .summary-box {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 10px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            width: 33%;
        }
        .summary-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 4px;
        }
        .summary-value {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-box td.highlight {
            background-color: #e8f4fd;
            border-bottom: 2px solid #3498db;
        }
        .summary-box td.highlight .summary-value {
            color: #2980b9;
        }

        /* Data Table (ANTI MEMANJANG) */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;

            /* KUNCI supaya kolom tidak melebar */
            table-layout: fixed;
        }
        .table-data th {
            background-color: #2c3e50;
            color: #fff;
            padding: 9px 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .table-data td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;

            /* KUNCI untuk teks panjang tanpa spasi */
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Kalau ada teks super panjang (aaaaaa...) paksa pecah */
        .force-break {
            word-break: break-all;
            overflow-wrap: anywhere;
        }

        /* Zebra Striping */
        .table-data tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #eee;
            color: #555;
        }
        .badge-verified {
            background-color: #dff5e6;
            color: #1e7a39;
        }
        .badge-rejected {
            background-color: #fde2e2;
            color: #b42318;
        }
        .badge-pending {
            background-color: #fff3cd;
            color: #8a6d3b;
        }

        .money-in { color: #27ae60; font-weight: bold; }
        .money-out { color: #c0392b; font-weight: bold; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #aaa;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }

        /* Hindari baris terpotong saat page break */
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
    </style>
</head>
<body>

@php
    // Pisahkan transaksi jadi 2 (pemasukan & pengeluaran)
    $txMasuk  = collect($transactions ?? [])->where('type', \App\Models\PembayaranKas::TYPE_IN)->values();
    $txKeluar = collect($transactions ?? [])->where('type', \App\Models\PembayaranKas::TYPE_OUT)->values();

    $statusBadgeClass = function ($status) {
        $s = strtolower((string) $status);
        return match ($s) {
            'confirmed' => 'badge-verified',
            'rejected'  => 'badge-rejected',
            'pending'   => 'badge-pending',
            default     => '',
        };
    };

    $statusLabel = function ($status) {
        $s = strtolower((string) $status);
        return match ($s) {
            'confirmed' => 'Verified',
            'rejected'  => 'Rejected',
            'pending'   => 'Pending',
            default     => ucfirst($s ?: '-'),
        };
    };

    // Pengeluaran: ambil kategori dari kolom category atau dari prefix [Kategori: ...]
    $categoryOf = function ($tx) {
        if (!empty($tx->category)) return $tx->category;

        $desc = (string) ($tx->description ?? '');
        if (preg_match('/^\[Kategori:\s*(.*?)\]\s*/', $desc, $m)) {
            return trim($m[1]);
        }
        return '-';
    };

    $cleanDesc = function ($tx) {
        $desc = (string) ($tx->description ?? '');
        $desc = preg_replace('/^\[Kategori:\s*(.*?)\]\s*/', '', $desc);
        $desc = trim($desc);
        return $desc !== '' ? $desc : '-';
    };
@endphp

    <div class="header">
        <table style="width: 100%">
            <tr>
                <td>
                    <h2>Rekapitulasi Kas</h2>
                    <div class="meta">
                        Dicetak pada: {{ now()->format('d M Y H:i') }}
                    </div>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    <div class="meta">
                        @if(!empty($from) && !empty($to))
                            <strong>Periode:</strong><br>
                            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                        @else
                            <strong>Periode:</strong> Semua Waktu
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary-box">
        <tr>
            <td>
                <span class="summary-label">Total Pemasukan</span>
                <span class="summary-value" style="color: #27ae60;">
                    Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                </span>
            </td>
            <td>
                <span class="summary-label">Total Pengeluaran</span>
                <span class="summary-value" style="color: #c0392b;">
                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                </span>
            </td>
            <td class="highlight">
                <span class="summary-label">Sisa Saldo</span>
                <span class="summary-value">
                    Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
                </span>
            </td>
        </tr>
    </table>

    {{-- =========================
        RIWAYAT PEMASUKAN
    ========================= --}}
    <div class="section-title">Riwayat Transaksi — Pemasukan</div>
    <div class="section-note">
        Menampilkan {{ $txMasuk->count() }} transaksi pemasukan (berdasarkan filter periode & pencarian yang dipakai pada laporan ini).
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="14%">Tanggal</th>
                <th width="22%">Nama / Pihak</th>
                <th width="34%">Nama Tagihan (Periode)</th>
                <th width="18%" class="text-right">Nominal</th>
                <th width="12%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($txMasuk as $tx)
                @php
                    $bill = $tx->bill ?? null;

                    // fleksibel: bill bisa punya title / name
                    $billName = $bill->title ?? $bill->name ?? ($tx->description ?? '-');

                    $billPeriod = null;
                    if (!empty($bill->period)) {
                        try {
                            $billPeriod = \Carbon\Carbon::parse($bill->period)->format('d M Y');
                        } catch (\Exception $e) {
                            $billPeriod = $bill->period;
                        }
                    } else {
                        $billPeriod = optional($tx->created_at)->format('d M Y');
                    }
                @endphp

                <tr>
                    <td>{{ optional($tx->created_at)->format('d/m/Y H:i') }}</td>

                    <td>
                        <div class="text-bold">{{ $tx->user->name ?? ($tx->payer_name ?? '-') }}</div>
                        <div class="text-muted" style="font-size:10px;">
                            {{ $tx->user->nim ?? ($tx->payer_nim ?? '') }}
                        </div>
                    </td>

                    <td class="force-break">
                        <div class="text-bold">{{ $billName }}</div>
                        <div class="text-muted" style="font-size:10px;">{{ $billPeriod }}</div>
                    </td>

                    <td class="text-right">
                        <span class="money-in">Rp {{ number_format($tx->amount ?? 0, 0, ',', '.') }}</span>
                    </td>

                    <td class="text-center">
                        <span class="badge {{ $statusBadgeClass($tx->status ?? '') }}">
                            {{ $statusLabel($tx->status ?? '') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 16px; color: #777;">
                        <em>Tidak ada data pemasukan pada periode ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- =========================
        RIWAYAT PENGELUARAN
    ========================= --}}
    <div class="section-title" style="margin-top: 18px;">Riwayat Transaksi — Pengeluaran</div>
    <div class="section-note">
        Menampilkan {{ $txKeluar->count() }} transaksi pengeluaran (berdasarkan filter periode & pencarian yang dipakai pada laporan ini).
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="14%">Tanggal</th>
                <th width="22%">Nama / Pihak</th>
                <th width="40%">Kategori & Keterangan</th>
                <th width="12%" class="text-right">Nominal</th>
                <th width="12%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($txKeluar as $tx)
                @php
                    $cat = $categoryOf($tx);
                    $desc = $cleanDesc($tx);
                @endphp

                <tr>
                    <td>{{ optional($tx->created_at)->format('d/m/Y H:i') }}</td>

                    <td>
                        <div class="text-bold">{{ $tx->user->name ?? ($tx->payer_name ?? '-') }}</div>
                        <div class="text-muted" style="font-size:10px;">
                            {{ $tx->user->nim ?? ($tx->payer_nim ?? '') }}
                        </div>
                    </td>

                    <td class="force-break">
                        <div class="text-bold">[{{ $cat }}]</div>
                        <div class="text-muted" style="font-size:10px;">{{ $desc }}</div>
                    </td>

                    <td class="text-right">
                        <span class="money-out">Rp {{ number_format($tx->amount ?? 0, 0, ',', '.') }}</span>
                    </td>

                    <td class="text-center">
                        <span class="badge {{ $statusBadgeClass($tx->status ?? '') }}">
                            {{ $statusLabel($tx->status ?? '') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 16px; color: #777;">
                        <em>Tidak ada data pengeluaran pada periode ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Halaman ini dibuat secara otomatis oleh sistem.
    </div>

</body>
</html>
