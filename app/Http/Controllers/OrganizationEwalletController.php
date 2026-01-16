<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Ewallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationEwalletController extends Controller
{
    public function store(Request $request, Organization $organization)
    {
        // (opsional) authorization:
        // $this->authorize('update', $organization);

        $validated = $request->validate([
            'provider'   => ['required', 'string', 'max:50'],   // gopay/ovo/dana/shopeepay/...
            'number'     => ['required', 'string', 'max:100'],  // no hp / no akun
            'owner_name' => ['required', 'string', 'max:150'],  // a.n.
        ]);

        $organization->ewallets()->create($validated);

        return back()->with('success', 'E-Wallet berhasil ditambahkan.');
    }

    // ✅ HAPUS E-WALLET
    public function destroy(Organization $organization, Ewallet $ewallet)
    {
        Gate::authorize('manageMembers', $organization);

        abort_unless($ewallet->organization_id === $organization->id, 404);

        $ewallet->delete();

        return back()->with('success', 'E-Wallet berhasil dihapus.');
    }
}
