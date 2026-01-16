<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilSayaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman Profil Saya
     * Route contoh:
     *   GET  /profil-saya   -> profil.saya.edit
     */
    public function edit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // context organisasi aktif untuk header (opsional)
        $orgId = session('active_organization_id');
        $activeOrganization = null;
        $activeRole = 'member';

        if ($orgId) {
            $activeOrganization = $user->organizations()
                ->where('organizations.id', $orgId)
                ->first();

            $activeRole = $activeOrganization?->pivot?->role ?? 'member';
        }

        /**
         * PENTING:
         * Pastikan file view sesuai.
         * - Jika file kamu: resources/views/profil-saya.blade.php  => view('profil-saya')
         * - Jika file kamu: resources/views/profil/saya.blade.php => view('profil.saya')
         */
        return view('profil-saya', compact('user', 'activeOrganization', 'activeRole'));
    }

    /**
     * Update Profil + Upload Foto
     * Route contoh:
     *   PUT /profil-saya -> profil.saya.update
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],

            // Upload foto (2MB) - jpg/jpeg/png/webp
            'photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            // hapus foto lama jika ada
            if (!empty($user->photo) && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // simpan foto baru
            $path = $request->file('photo')->store('profile-photos', 'public');
            $data['photo'] = $path;
        } else {
            // jangan timpa photo jadi null kalau user tidak upload
            unset($data['photo']);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * (Opsional) Hapus foto profil
     * Kalau kamu mau tombol "hapus foto" di UI.
     */
    public function deletePhoto(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!empty($user->photo) && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->update(['photo' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
