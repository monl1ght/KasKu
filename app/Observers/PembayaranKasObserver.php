<?php

namespace App\Observers;

use App\Models\PembayaranKas;
use App\Models\PembayaranKasActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PembayaranKasObserver
{
    private array $watchKeys = [
        'status',
        'amount',
        'type',
        'bill_id',
        'description',
        'payment_date',
        'receipt_path',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'user_id',
    ];

    protected function actorAndRole(?int $orgId): array
    {
        $actor = Auth::user();
        if (!$actor || !$orgId) return [null, null];

        $membership = $actor->organizations()
            ->where('organizations.id', $orgId)
            ->first();

        $role = $membership?->pivot?->role;

        return [$actor, $role];
    }

    protected function write(PembayaranKas $pk, string $action, ?array $old, ?array $new): void
    {
        $orgId = $pk->organization_id;
        [$actor, $role] = $this->actorAndRole($orgId);

        // hanya log aksi admin/bendahara
        if (!$actor || $role !== 'admin') {
            return;
        }

        PembayaranKasActivityLog::create([
            'organization_id'   => $orgId,
            'pembayaran_kas_id' => $pk->id,
            'actor_id'          => $actor->id,
            'actor_role'        => $role,
            'payer_id'          => $pk->user_id,

            'action'            => $action,
            'old_values'        => $old,
            'new_values'        => $new,

            'ip_address'        => Request::ip(),
            'user_agent'        => substr((string) Request::userAgent(), 0, 255),
        ]);
    }

    protected function snapshot(PembayaranKas $pk): array
    {
        // snapshot yang “bernilai audit”
        return [
            'status'          => $pk->status,
            'type'            => $pk->type,
            'amount'          => $pk->amount,
            'bill_id'         => $pk->bill_id,
            'user_id'         => $pk->user_id,
            'payment_date'    => $pk->payment_date,
            'receipt_path'    => $pk->receipt_path,
            'verified_at'     => $pk->verified_at,
            'verified_by'     => $pk->verified_by,
            'rejection_reason'=> $pk->rejection_reason,
            'description'     => $pk->description,
            'created_at'      => optional($pk->created_at)->toDateTimeString(),
            'updated_at'      => optional($pk->updated_at)->toDateTimeString(),
        ];
    }

    public function created(PembayaranKas $pk): void
    {
        $this->write($pk, 'create', null, $this->snapshot($pk));
    }

    public function updated(PembayaranKas $pk): void
    {
        $changes = $pk->getChanges();
        $watched = array_intersect_key($changes, array_flip($this->watchKeys));

        // tidak ada perubahan penting -> skip
        if (empty($watched)) return;

        $old = [];
        foreach ($watched as $key => $val) {
            $old[$key] = $pk->getOriginal($key);
        }

        // label aksi yang lebih spesifik
        $action = 'update';
        if (array_key_exists('status', $watched)) {
            $action = 'status_change';
        } elseif (array_key_exists('amount', $watched)) {
            $action = 'amount_change';
        } elseif (array_key_exists('payment_date', $watched)) {
            $action = 'date_change';
        } elseif (array_key_exists('receipt_path', $watched)) {
            $action = 'receipt_change';
        }

        $this->write($pk, $action, $old, $watched);
    }

    public function deleting(PembayaranKas $pk): void
    {
        $this->write($pk, 'delete', $this->snapshot($pk), null);
    }
}
