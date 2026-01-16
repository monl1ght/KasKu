<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Riwayat Transaksi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .meta {
            font-size: 10px;
            color: #555;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }

        .b-verified {
            background: #dcfce7;
            color: #166534;
        }

        .b-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .b-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .small {
            font-size: 10px;
            color: #555;
        }

        .img {
            margin-top: 6px;
        }

        .img img {
            max-width: 240px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="title">Riwayat Transaksi</div>

    <div class="meta">
        Dibuat: {{ $meta['generated_at']->format('d/m/Y H:i') }}<br>
        Filter:
        Status={{ $meta['filters']['status'] ?? 'all' }},
        Q={{ $meta['filters']['q'] ?: '-' }},
        Dari={{ $meta['filters']['from'] ?: '-' }},
        Sampai={{ $meta['filters']['to'] ?: '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 90px;">ID</th>
                <th>Tagihan</th>
                <th style="width: 80px;">Kategori</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 90px;">Status</th>
                <th style="width: 95px;" class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td>{{ $r['trx_code'] }}</td>
                    <td>
                        <strong>{{ $r['charge_name'] }}</strong><br>
                        @if (!empty($r['notes']))
                            <span class="small">Catatan: {{ $r['notes'] }}</span><br>
                        @endif
                        @if (($r['status_ui'] ?? '') === 'rejected' && !empty($r['rejection_reason']))
                            <span class="small">Alasan: {{ $r['rejection_reason'] }}</span><br>
                        @endif
                        @if (!empty($r['verified_by']))
                            <span class="small">Diverifikasi: {{ $r['verified_by'] }}</span>
                        @endif

                        @php
                            $p = $r['proof_local_path'] ?? null;
                        @endphp

                        @if ($p && file_exists($p))
                            <div class="img">
                                <span class="small">Bukti:</span><br>
                                <img src="{{ $p }}" alt="Bukti Transfer">
                            </div>
                        @endif
                    </td>
                    <td>{{ $r['category'] }}</td>
                    <td>{{ optional($r['created_at'])->format('d/m/Y') }}</td>
                    <td>
                        @php($s = $r['status_ui'] ?? 'pending')
                        <span
                            class="badge
                        {{ $s === 'verified' ? 'b-verified' : '' }}
                        {{ $s === 'pending' ? 'b-pending' : '' }}
                        {{ $s === 'rejected' ? 'b-rejected' : '' }}
                    ">
                            {{ $s === 'verified' ? 'Terverifikasi' : ($s === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                        </span>
                    </td>
                    <td class="right">Rp {{ number_format((float) ($r['amount'] ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
