<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Organization;
use App\Models\PembayaranKas; // gunakan model kalau ada konstanta status
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select');
        }

        $activeOrganization = Organization::findOrFail($orgId);
        $totalMembers = $activeOrganization->users()->count();

        // ✅ ambil query search dari URL: ?search=...
        $search = trim((string) $request->query('search', ''));

        // Base query bills (dipakai untuk list & statistik)
        $baseBillsQuery = Bill::where('organization_id', $orgId)
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });

        // ✅ PAGINATION untuk list (ini yang dipakai di blade table)
        $bills = (clone $baseBillsQuery)
            ->select('id', 'name', 'amount', 'due_date')
            ->latest()
            ->paginate(10)
            ->withQueryString(); // ✅ supaya pagination bawa ?search=...

        // Bill IDs (khusus halaman ini, untuk progress per bill)
        $billIdsPage = $bills->getCollection()->pluck('id')->filter()->values();

        // Bill IDs (SEMUA yang match search, untuk statistik atas)
        $billIdsAll = (clone $baseBillsQuery)->pluck('id')->filter()->values();

        $validStatuses = ['confirmed', 'approved', 'success'];

        // =========================
        // 1) Agregasi PER HALAMAN (buat progress tiap bill)
        // =========================
        $paymentsAggPage = $billIdsPage->isEmpty()
            ? collect()
            : DB::table('pembayaran_kas')
            ->whereIn('bill_id', $billIdsPage)
            ->whereIn('status', $validStatuses)
            ->selectRaw('bill_id, COUNT(DISTINCT user_id) as paid_unique_count, COALESCE(SUM(amount),0) as paid_amount')
            ->groupBy('bill_id')
            ->get()
            ->keyBy('bill_id');

        // Attach data progress ke bills yang tampil di halaman ini
        foreach ($bills->getCollection() as $bill) {
            $agg = $paymentsAggPage->get($bill->id);

            $paidUnique = $agg ? (int) $agg->paid_unique_count : 0;
            $paidMoney  = $agg ? (float) $agg->paid_amount : 0.0;

            $progressPercent = $totalMembers > 0
                ? (int) round(($paidUnique / $totalMembers) * 100)
                : 0;

            $progressPercent = max(0, min(100, $progressPercent));

            $bill->target_members    = $totalMembers;
            $bill->paid_unique_count = $paidUnique;
            $bill->paid_amount       = $paidMoney;
            $bill->progress_percent  = $progressPercent;
        }

        // =========================
        // 2) Statistik GLOBAL (kartu atas) — ikut search
        // =========================
        $totalTagihan = $billIdsAll->count();

        // total nilai: sum(amount bill) * total anggota
        $sumAmountAll = (clone $baseBillsQuery)->sum('amount');
        $totalNilai = (float) $sumAmountAll * (int) $totalMembers;

        // total sudah dibayar (uang) dari SEMUA bill yang match search
        $totalSudahDibayar = $billIdsAll->isEmpty()
            ? 0
            : (float) DB::table('pembayaran_kas')
                ->whereIn('bill_id', $billIdsAll)
                ->whereIn('status', $validStatuses)
                ->sum('amount');

        $totalBelumDibayar = max(0, $totalNilai - $totalSudahDibayar);

        $globalProgress = $totalNilai > 0
            ? (int) round(($totalSudahDibayar / $totalNilai) * 100)
            : 0;

        $globalProgress = max(0, min(100, $globalProgress));

        return view('ManajemenTagihan', compact(
            'activeOrganization',
            'bills',
            'totalTagihan',
            'totalNilai',
            'totalSudahDibayar',
            'totalBelumDibayar',
            'globalProgress'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ]);

        // Pastikan Bill model punya fillable untuk fields ini
        Bill::create([
            'organization_id' => session('active_organization_id'),
            'name' => $request->name,
            'due_date' => $request->due_date,
            'amount' => $request->amount,
        ]);

        return redirect()
            ->route('manajemen.tagihan')
            ->with('success', 'Tagihan berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $bill = Bill::findOrFail($id);

        if ($bill->organization_id !== session('active_organization_id')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ]);

        $bill->update($request->only('name', 'due_date', 'amount'));

        return back()->with('success', 'Tagihan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bill = Bill::findOrFail($id);

        if ($bill->organization_id !== session('active_organization_id')) {
            abort(403);
        }

        $bill->delete();

        return back()->with('success', 'Tagihan berhasil dihapus');
    }
}
