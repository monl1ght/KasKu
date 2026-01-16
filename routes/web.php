<?php

use App\Http\Controllers\ProfilSayaController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\MemberAdminPaymentLogController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationMemberController;
use App\Http\Controllers\JoinRequestController;
use App\Http\Controllers\PembayaranKasController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\LaporanRekapitulasiController;
use App\Http\Controllers\OrganizationEwalletController;
use App\Http\Controllers\OrganizationBankController;
use App\Http\Controllers\PengeluaranKasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTH (TANPA VERIFIED)
|--------------------------------------------------------------------------
| Login tidak wajib verifikasi email.
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | ORGANIZATION
    |----------------------------------------------------------------------
    */

    Route::post('/organizations', [OrganizationController::class, 'store'])
        ->name('organizations.store');

    Route::get('/organization/{id}/access', function ($id) {
        $org = Auth::user()->organizations()->findOrFail($id);
        session(['active_organization_id' => $org->id]);

        $membership = $org->users()
            ->where('users.id', Auth::id())
            ->first();

        $role = $membership ? ($membership->pivot->role ?? 'member') : 'member';

        if ($role === 'admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('member.dashboard');
    })->name('organization.access');

    Route::get('/select-organization', function () {
        $organizations = Auth::user()
            ->organizations()
            ->wherePivot('role', 'admin')
            ->get();

        return view('select-organization', compact('organizations'));
    })->name('organization.select');

    /*
    |----------------------------------------------------------------------
    | DASHBOARD
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | MEMBER (ANGGOTA) AREA
    |----------------------------------------------------------------------
    */
    Route::get('/member/dashboard', [\App\Http\Controllers\MemberDashboardController::class, 'index'])
        ->name('member.dashboard');

    Route::get('/member/PembayaranKas', [PembayaranKasController::class, 'index'])
        ->name('member.PembayaranKas');

    Route::get('/member/PengaturanProfil', function () {
        $user = Auth::user();
        $orgId = session('active_organization_id');

        if (! $orgId) {
            $firstOrgId = $user->organizations()
                ->orderBy('organization_user.created_at', 'asc')
                ->value('organizations.id');

            if ($firstOrgId) {
                session(['active_organization_id' => $firstOrgId]);
                $orgId = $firstOrgId;
            }
        }

        if (! $orgId) {
            return view('member.PengaturanProfil', [
                'organization' => null,
            ]);
        }

        $organization = $user->organizations()
            ->with(['bankAccounts', 'ewallets'])
            ->where('organizations.id', $orgId)
            ->first();

        if (! $organization) {
            session()->forget('active_organization_id');

            return view('member.PengaturanProfil', [
                'organization' => null,
            ]);
        }

        return view('member.PengaturanProfil', compact('organization'));
    })->name('member.PengaturanProfil');

    Route::prefix('member')->group(function () {
        Route::get('/RiwayatTransaksi', [RiwayatTransaksiController::class, 'index'])
            ->name('member.RiwayatTransaksi');

        Route::get('/RiwayatTransaksi/export-pdf', [RiwayatTransaksiController::class, 'exportPdf'])
            ->name('member.riwayat-transaksi.export-pdf');
    });

    /*
    |----------------------------------------------------------------------
    | JOIN ORGANIZATION
    |----------------------------------------------------------------------
    */
    Route::post('/join-organization', [OrganizationMemberController::class, 'joinByCode'])
        ->name('organization.join');

    /*
    |----------------------------------------------------------------------
    | MEMBER REQUESTS
    |----------------------------------------------------------------------
    */
    Route::get('/member-requests', [JoinRequestController::class, 'index'])
        ->name('member.requests');

    Route::post('/member-requests/{joinRequest}/approve', [JoinRequestController::class, 'approve'])
        ->name('member.requests.approve');

    Route::post('/member-requests/{joinRequest}/reject', [JoinRequestController::class, 'reject'])
        ->name('member.requests.reject');

    /*
    |----------------------------------------------------------------------
    | MANAJEMEN ANGGOTA
    |----------------------------------------------------------------------
    */
    Route::get('/ManajemenAnggota', [OrganizationMemberController::class, 'index'])->name('members.index');
    Route::get('/ManajemenAnggota/export', [OrganizationMemberController::class, 'export'])->name('members.export');
    Route::get('/ManajemenAnggota/{user}', [OrganizationMemberController::class, 'show'])->name('members.show');
    Route::patch('/ManajemenAnggota/{user}/deactivate', [OrganizationMemberController::class, 'deactivate'])->name('members.deactivate');
    Route::patch('/ManajemenAnggota/{user}/activate', [OrganizationMemberController::class, 'activate'])->name('members.activate');
    Route::delete('/ManajemenAnggota/{user}/kick', [OrganizationMemberController::class, 'kick'])->name('members.kick');

    /*
    |----------------------------------------------------------------------
    | TAGIHAN (BILLS)
    |----------------------------------------------------------------------
    */
    Route::get('/ManajemenTagihan', [BillController::class, 'index'])
        ->name('manajemen.tagihan');

    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');
    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');

    /*
    |----------------------------------------------------------------------
    | PEMBAYARAN KAS
    |----------------------------------------------------------------------
    */
    Route::get('/pembayaran-kas', [PembayaranKasController::class, 'index'])->name('pembayaran-kas.index');
    Route::get('/pembayaran-kas/create', [PembayaranKasController::class, 'create'])->name('pembayaran-kas.create');
    Route::post('/pembayaran-kas', [PembayaranKasController::class, 'store'])->name('pembayaran-kas.store');
    Route::get('/pembayaran-kas/{pembayaranKas}/edit', [PembayaranKasController::class, 'edit'])->name('pembayaran-kas.edit');
    Route::put('/pembayaran-kas/{pembayaranKas}', [PembayaranKasController::class, 'update'])->name('pembayaran-kas.update');
    Route::delete('/pembayaran-kas/{pembayaranKas}', [PembayaranKasController::class, 'destroy'])->name('pembayaran-kas.destroy');
    Route::get('/pembayaran-kas/{pembayaranKas}/receipt', [PembayaranKasController::class, 'downloadReceipt'])->name('pembayaran-kas.receipt');

    /*
    |----------------------------------------------------------------------
    | VERIFIKASI PEMBAYARAN
    |----------------------------------------------------------------------
    */
    Route::get('/VerifikasiPembayaran', [VerificationController::class, 'index'])->name('verifikasi.pembayaran');
    Route::post('/VerifikasiPembayaran/{pembayaranKas}/approve', [VerificationController::class, 'approve'])->name('verifikasi.pembayaran.approve');
    Route::post('/VerifikasiPembayaran/{pembayaranKas}/reject', [VerificationController::class, 'reject'])->name('verifikasi.pembayaran.reject');
    Route::get('/VerifikasiPembayaran/{pembayaranKas}/download', [VerificationController::class, 'download'])->name('verifikasi.pembayaran.download');
    Route::get('/verifikasi/pembayaran/{pembayaranKas}/receipt', [VerificationController::class, 'showReceipt'])->name('verifikasi.pembayaran.receipt');

    /*
    |----------------------------------------------------------------------
    | PENGELUARAN KAS
    |----------------------------------------------------------------------
    */
    Route::get('/PengeluaranKas', [PengeluaranKasController::class, 'index'])->name('pengeluaran-kas.index');
    Route::get('/PengeluaranKas/create', [PengeluaranKasController::class, 'create'])->name('pengeluaran-kas.create');
    Route::post('/PengeluaranKas', [PengeluaranKasController::class, 'store'])->name('pengeluaran-kas.store');
    Route::get('/PengeluaranKas/{pembayaranKas}/edit', [PengeluaranKasController::class, 'edit'])->name('pengeluaran-kas.edit');
    Route::put('/PengeluaranKas/{pembayaranKas}', [PengeluaranKasController::class, 'update'])->name('pengeluaran-kas.update');
    Route::get('/PengeluaranKas/{pembayaranKas}/receipt', [PengeluaranKasController::class, 'showReceipt'])->name('pengeluaran-kas.receipt');
    Route::delete('/PengeluaranKas/{pembayaranKas}', [PengeluaranKasController::class, 'destroy'])->name('pengeluaran-kas.destroy');

    /*
    |----------------------------------------------------------------------
    | LAPORAN
    |----------------------------------------------------------------------
    */
    Route::get('/LaporanRekapitulasi/pdf', [LaporanRekapitulasiController::class, 'exportPdf'])->name('laporan.rekapitulasi.pdf');
    Route::get('/laporan/receipt/{pembayaranKas}', [LaporanRekapitulasiController::class, 'showReceipt'])->name('laporan.receipt.show');
    Route::get('/laporan/tunggakan/{user}', [LaporanRekapitulasiController::class, 'detailTunggakan'])->name('laporan.tunggakan.detail');
    Route::get('/LaporanRekapitulasi', [LaporanRekapitulasiController::class, 'index'])->name('laporan.rekapitulasi');

    /*
    |----------------------------------------------------------------------
    | PENGATURAN ORGANISASI
    |----------------------------------------------------------------------
    */
    Route::get('/PengaturanOrganisasi', [OrganizationController::class, 'edit'])->name('organization.edit');
    Route::put('/PengaturanOrganisasi', [OrganizationController::class, 'update'])->name('organization.update');
    Route::patch('/organizations/{organization}/logo', [OrganizationController::class, 'updateLogo'])->name('organization.logo.update');

    Route::post('/organizations/{organization}/bank', [OrganizationBankController::class, 'store'])->name('organization.bank.store');
    Route::delete('/organizations/{organization}/bank/{bankAccount}', [OrganizationBankController::class, 'destroy'])->name('organization.bank.destroy');

    Route::post('/organizations/{organization}/ewallet', [OrganizationEwalletController::class, 'store'])->name('organization.ewallet.store');
    Route::delete('/organizations/{organization}/ewallet/{ewallet}', [OrganizationEwalletController::class, 'destroy'])->name('organization.ewallet.destroy');

    Route::delete('/PengaturanOrganisasi', [OrganizationController::class, 'destroy'])
        ->name('organization.destroy');


    /*
    |----------------------------------------------------------------------
    | ADMIN PAYMENT LOG
    |----------------------------------------------------------------------
    */
    Route::get('/member/admin-payment-log', [MemberAdminPaymentLogController::class, 'index'])
        ->name('member.admin_payment_log');

    /*
    |----------------------------------------------------------------------
    | PROFIL
    |----------------------------------------------------------------------
    */
    Route::get('/profil-saya', [ProfilSayaController::class, 'edit'])->name('profil.saya.edit');
    Route::put('/profil-saya', [ProfilSayaController::class, 'update'])->name('profil.saya.update');

    // ✅ alias untuk link/menu lama anggota (tanpa duplikasi name)
    Route::get('/profil-saya-anggota', function () {
        return redirect()->route('profil.saya.edit');
    })->name('profil.saya.anggota');
});

require __DIR__ . '/auth.php';
