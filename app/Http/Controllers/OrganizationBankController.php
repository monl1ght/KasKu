<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\BankAccount;

class OrganizationBankController extends Controller
{
    public function store(Request $request, Organization $organization)
    {
        // (opsional) authorization:
        // $this->authorize('update', $organization);

        $validated = $request->validate([
            'bank_name'  => ['required', 'string', 'max:100'],
            'number'     => ['required', 'string', 'max:100'],
            'owner_name' => ['required', 'string', 'max:150'],
        ]);

        $organization->bankAccounts()->create($validated);

        return back()->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    // ✅ HAPUS BANK
    public function destroy(Organization $organization, BankAccount $bankAccount)
    {
        Gate::authorize('manageMembers', $organization);

        // pastikan rekening milik organisasi aktif
        abort_unless($bankAccount->organization_id === $organization->id, 404);

        $bankAccount->delete();

        return back()->with('success', 'Rekening bank berhasil dihapus.');
    }
}
