<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Models\Organization;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;


class User extends Authenticatable implements MustVerifyEmail // + implements ini
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // profil
        'phone',
        'address',
        'photo',

        'google_id',

        // untuk manajemen anggota (kalau masih dipakai di fitur lain)
        'nim',
        'status',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for arrays / serialization.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',

    ];

    /**
     * Relasi ke Model Organization.
     * Menghubungkan User dengan banyak Organisasi.
     *
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany
    {
        // withPivot('role') penting agar kita bisa tahu siapa admin/member
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Accessor: joined_at (alias dari created_at)
     *
     * @return Carbon|null
     */
    public function getJoinedAtAttribute(): ?Carbon
    {
        return $this->created_at;
    }

    /**
     * Accessor: last_seen (human readable last activity)
     *
     * @return string|null
     */
    public function getLastSeenAttribute(): ?string
    {
        return $this->last_activity ? $this->last_activity->diffForHumans() : null;
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public function pembayaranKas()
    {
        return $this->hasMany(PembayaranKas::class);
    }
}
