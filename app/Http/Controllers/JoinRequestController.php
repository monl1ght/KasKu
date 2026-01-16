<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class JoinRequestController extends Controller
{
    public function index(Request $request)
    {
        $orgId = session('active_organization_id');
        if (! $orgId) {
            return redirect()->route('organization.select')
                ->with('error', 'Pilih organisasi terlebih dahulu.');
        }

        $user = $request->user();
        $membership = $user->organizations()
            ->where('organizations.id', $orgId)
            ->first();

        if (! $membership || ($membership->pivot->role ?? '') !== 'admin') {
            abort(403);
        }

        $requests = JoinRequest::with('user')
            ->where('organization_id', $orgId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('members.requests', compact('requests'));
    }

    public function approve(Request $request, JoinRequest $joinRequest)
    {
        $orgId = session('active_organization_id');
        if (! $orgId || $joinRequest->organization_id != $orgId) {
            abort(403);
        }

        $admin = $request->user();
        $membership = $admin->organizations()
            ->where('organizations.id', $orgId)
            ->first();

        if (! $membership || ($membership->pivot->role ?? '') !== 'admin') {
            abort(403);
        }

        DB::transaction(function () use ($joinRequest, $orgId) {

            // attach user ke organisasi
            $joinRequest->user->organizations()->attach(
                $orgId,
                ['role' => 'member']
            );

            // 🔥 HAPUS join request agar user bisa join ulang di masa depan
            $joinRequest->delete();
        });

        return back()->with('success', 'Permintaan anggota disetujui.');
    }

    public function reject(Request $request, JoinRequest $joinRequest)
    {
        $orgId = session('active_organization_id');
        if (! $orgId || $joinRequest->organization_id != $orgId) {
            abort(403);
        }

        $admin = $request->user();
        $membership = $admin->organizations()
            ->where('organizations.id', $orgId)
            ->first();

        if (! $membership || ($membership->pivot->role ?? '') !== 'admin') {
            abort(403);
        }

        // 🔥 HAPUS join request (bukan cuma update status)
        $joinRequest->delete();

        return back()->with('success', 'Permintaan anggota ditolak.');
    }
}
