<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category_id',
    'name',
    'description',
    'price',
    'stock_quantity',
    'image',
    'is_active'])]
class Product extends Model
{
    public function cast():array
    {
        return [
            'is_active'=> 'boolean',
            'price'=> 'decimal:2',
        ];
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
