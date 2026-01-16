<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    /**
     * Helper: ambil role pivot user pada organization (admin, member, bendahara, dll) atau null.
     */
    private function pivotRole(User $user, Organization $org): ?string
    {
        $membership = $user->organizations()
            ->where('organizations.id', $org->id)
            ->first();

        return $membership ? ($membership->pivot->role ?? null) : null;
    }

    /**
     * Determine whether the user can view any organizations (index).
     * Contoh: user yang punya minimal 1 organisasi boleh lihat daftar organisasi miliknya.
     */
    public function viewAny(User $user): bool
    {
        return $user->organizations()->exists();
    }

    /**
     * Determine whether the user can view the organization.
     */
    public function view(User $user, Organization $organization): Response
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists()
            ? Response::allow()
            : Response::deny('Anda bukan anggota organisasi ini.');
    }

    /**
     * Determine whether the user can create organizations.
     * Kebijakan: setiap user terautentikasi boleh membuat organisasi.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the organization.
     * Contoh: hanya admin di organisasi atau pembuat organisasi (created_by) yang bisa update.
     */
    public function update(User $user, Organization $organization): Response
    {
        $role = $this->pivotRole($user, $organization);

        if ($organization->created_by === $user->id || $role === 'admin') {
            return Response::allow();
        }

        return Response::deny('Hanya admin atau pembuat organisasi yang boleh mengubah organisasi ini.');
    }

    /**
     * Determine whether the user can delete the organization.
     * Hanya admin atau pembuat organisasi.
     */
    public function delete(User $user, Organization $organization): Response
    {
        $role = $this->pivotRole($user, $organization);

        if ($organization->created_by === $user->id || $role === 'admin') {
            return Response::allow();
        }

        return Response::deny('Hanya admin atau pembuat organisasi yang boleh menghapus organisasi ini.');
    }

    public function restore(User $user, Organization $organization): bool
    {
        // sama aturan dengan delete (ubah sesuai kebutuhan)
        return ($organization->created_by === $user->id) || ($this->pivotRole($user, $organization) === 'admin');
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        // risiko besar — batasi ke pembuat saja
        return $organization->created_by === $user->id;
    }

    //
    // === Custom rules untuk fitur anggota / pembayaran ===
    //

    /**
     * Kelola anggota: hanya admin yang boleh manage members.
     */
    public function manageMembers(User $user, Organization $organization): Response
    {
        return $this->pivotRole($user, $organization) === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang dapat mengelola anggota.');
    }

    /**
     * Lihat daftar pembayaran: cukup menjadi anggota organisasi.
     */
    public function viewAnyPayments(User $user, Organization $organization): Response
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists()
            ? Response::allow()
            : Response::deny('Anda bukan anggota organisasi ini.');
    }

    /**
     * Membuat pembayaran: anggota (atau role tertentu) boleh membuat.
     */
    public function createPayment(User $user, Organization $organization): Response
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists()
            ? Response::allow()
            : Response::deny('Hanya anggota yang dapat menambahkan pembayaran.');
    }

    /**
     * Update pembayaran: admin atau bendahara (treasurer) boleh update.
     */
    public function updatePayment(User $user, Organization $organization): Response
    {
        $role = $this->pivotRole($user, $organization);

        return in_array($role, ['admin', 'bendahara', 'treasurer'], true)
            ? Response::allow()
            : Response::deny('Hanya admin atau bendahara yang dapat mengubah pembayaran.');
    }

    /**
     * Delete pembayaran: hanya admin.
     */
    public function deletePayment(User $user, Organization $organization): Response
    {
        return $this->pivotRole($user, $organization) === 'admin'
            ? Response::allow()
            : Response::deny('Hanya admin yang dapat menghapus pembayaran.');
    }
}
