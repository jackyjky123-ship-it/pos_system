<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id',
    'sale_id',
    'product_id',
    'product_name',
    'unit_price',
    'quantity',
    'subtotal',

])]
class SaleItem extends Model
{
    public function cast(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
