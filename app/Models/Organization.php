<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- Tambahkan ini
use App\Models\Ewallet;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'account_number',

        // ⬇️ TAMBAHKAN INI
        'short_name',
        'email',
        'phone',
        'address',
        'logo_path',
    ];

    /**
     * Relasi ke User (Anggota)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Relasi ke Tagihan (Bills)
     * FUNGSI INI YANG TADI HILANG/BELUM ADA
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    // jika mau ewallets juga
    public function ewallets(): HasMany
    {
        return $this->hasMany(Ewallet::class);
    }
}


