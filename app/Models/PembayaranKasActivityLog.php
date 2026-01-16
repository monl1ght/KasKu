<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranKasActivityLog extends Model
{
    protected $table = 'pembayaran_kas_activity_logs';

    protected $fillable = [
        'organization_id',
        'pembayaran_kas_id',
        'actor_id',
        'actor_role',
        'payer_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function pembayaranKas(): BelongsTo
    {
        return $this->belongsTo(PembayaranKas::class, 'pembayaran_kas_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }
}
