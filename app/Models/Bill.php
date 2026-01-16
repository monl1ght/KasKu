<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',       // ⬅️ TAMBAHAN AMAN (tidak merusak)
        'name',
        'amount',
        'due_date',
        'status',        // ⬅️ TAMBAHAN AMAN
    ];

    protected $casts = [
        'due_date' => 'date', // ⬅️ INI PENTING
        'amount'   => 'decimal:2',
    ];

    /* =======================
     |  STATUS CONSTANTS
     |  (DITAMBAHKAN – AMAN)
     ======================= */
    public const STATUS_UNPAID  = 'unpaid';
    public const STATUS_PAID    = 'paid';
    public const STATUS_PARTIAL = 'partial';

    /* =======================
     |  RELATIONS
     ======================= */

    // Relasi: Tagihan milik satu organisasi
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // ⬅️ TAMBAHAN: Tagihan milik satu anggota (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Tagihan memiliki banyak pembayaran
    public function payments()
    {
        return $this->hasMany(PembayaranKas::class, 'bill_id');
    }

    /* =======================
     |  SCOPES (AMAN)
     ======================= */

    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    public function scopeOrg($query, $orgId)
    {
        if ($orgId === null) return $query;
        return $query->where('organization_id', $orgId);
    }

    /* =======================
     |  ACCESSORS / HELPERS
     ======================= */

    /**
     * Durasi tunggakan dalam bulan (untuk badge "1 Bulan", "4 Bulan")
     */
    public function getOverdueMonthsAttribute(): int
    {
        if (!$this->due_date) return 0;
        if (($this->status ?? null) !== self::STATUS_UNPAID) return 0;

        return max(
            1,
            Carbon::parse($this->due_date)->diffInMonths(now())
        );
    }

    /**
     * Format nominal: Rp 150.000
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }
}
