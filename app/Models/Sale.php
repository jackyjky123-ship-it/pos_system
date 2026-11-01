<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id',
    'user_id',
    'total_amount',
    'cash_tendered',
    'cash_change',
    'status',
    'notes',

])]
class Sale extends Model
{
    public function cast(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'cash_tendered' => 'decimal:2',
            'cash_change' => 'decimal:2',
        ];
    }
    public function items():HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
