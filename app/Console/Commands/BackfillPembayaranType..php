<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillPembayaranType extends Command
{
    protected $signature = 'pembayaran:backfill-type {--dry}';
    protected $description = 'Backfill kolom `type` pada table pembayaran_kas dengan heuristik.';

    public function handle()
    {
        $this->info('Mulai backfill pembayaran_kas.type ...');
        $rows = DB::table('pembayaran_kas')->whereNull('type')->get();

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        $report = ['set_pemasukan' => 0, 'set_pengeluaran' => 0, 'ambiguous' => 0];

        foreach ($rows as $r) {
            // Heuristik (ubah sesuai kebutuhan):
            // - Jika bill_id != null => pagar pembayaran tagihan -> pemasukan
            // - Jika description mengandung kata pembelian/gaji/honor -> pengeluaran
            $type = null;
            if (!empty($r->bill_id)) {
                $type = 'pemasukan';
            } else {
                $desc = strtolower($r->description ?? '');
                if (preg_match('/(pembelian|beli|gaji|honor|operasional|pengeluaran|bayar supplier)/', $desc)) {
                    $type = 'pengeluaran';
                } else {
                    // fallback: treat confirmed as pemasukan
                    if ($r->status === 'confirmed') $type = 'pemasukan';
                }
            }

            if ($this->option('dry')) {
                if ($type === null) $report['ambiguous']++;
                elseif ($type === 'pemasukan') $report['set_pemasukan']++;
                else $report['set_pengeluaran']++;
            } else {
                if ($type) {
                    DB::table('pembayaran_kas')->where('id', $r->id)->update(['type' => $type]);
                    $report[$type === 'pemasukan' ? 'set_pemasukan' : 'set_pengeluaran']++;
                } else {
                    $report['ambiguous']++;
                    // mark for manual review
                    DB::table('pembayaran_kas')->where('id', $r->id)->update(['type' => 'pemasukan']); // fallback, optional
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Done. Report: ' . json_encode($report));
    }
}
