<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    protected $fillable = ['organization_id','bank_name','number','owner_name'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
