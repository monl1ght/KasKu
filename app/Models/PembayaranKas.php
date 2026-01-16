<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PembayaranKas extends Model
{
    protected $table = 'pembayaran_kas';

    /**
     * Status constants (WAJIB konsisten di seluruh app)
     */
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED  = 'rejected';

    /**
     * Type constants (DITAMBAHKAN agar controller laporan bisa memakai)
     */
    public const TYPE_IN  = 'pemasukan';
    public const TYPE_OUT = 'pengeluaran';

    /**
     * Kolom yang boleh di-mass assign
     */
    protected $fillable = [
        'organization_id',
        'user_id',
        'bill_id',

        'type',
        'amount',
        'currency',
        'status',
        'description',

        'payment_date',
        'receipt_path',

        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'payment_date' => 'date',
        'verified_at'  => 'datetime',
        'amount'       => 'decimal:2',
    ];

    /* =======================
     |  RELATIONS
     ======================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Admin / bendahara yang memverifikasi
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /* =======================
     |  SCOPES (opsional tapi sangat berguna)
     ======================= */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // -----------------------
    // TAMBAHAN AMAN UNTUK LAPORAN
    // -----------------------

    /**
     * Filter berdasarkan organization_id (aman: tidak merubah perilaku yg sudah ada)
     */
    public function scopeOrg($query, $orgId)
    {
        if ($orgId === null) {
            return $query;
        }
        return $query->where('organization_id', $orgId);
    }

    /**
     * Filter rentang tanggal berdasarkan kolom payment_date
     * $from / $to dapat berupa string/Carbon
     */
    public function scopeBetweenDate($query, $from = null, $to = null)
    {
        if ($from && $to) {
            return $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }

        if ($from) {
            return $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to) {
            return $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        return $query;
    }
    /**
     * Filter berdasarkan type
     */
    public function scopeTypeIn($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    public function scopeTypeOut($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    /* =======================
     |  ACCESSORS / HELPERS
     ======================= */

    /**
     * Dapatkan formatted amount untuk ditampilkan di blade
     * Contoh: "Rp 150.000"
     */
    public function getFormattedAmountAttribute(): string
    {
        // jika null atau 0 tetap format aman
        $amount = $this->amount ?? 0;
        // pastikan integer/float
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    /**
     * Convenience helper: apakah transaksi ini pemasukan?
     */
    public function getIsIncomeAttribute(): bool
    {
        return ($this->type ?? null) === self::TYPE_IN;
    }

    /**
     * Convenience helper: apakah transaksi ini pengeluaran?
     */
    public function getIsExpenseAttribute(): bool
    {
        return ($this->type ?? null) === self::TYPE_OUT;
    }
}
