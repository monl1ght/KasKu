<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'status',
        'handled_by',
        'handled_at',
        'message',
    ];

    // ✅ INI YANG KAMU LUPA
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
