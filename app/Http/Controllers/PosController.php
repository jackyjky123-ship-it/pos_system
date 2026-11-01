<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PosController extends Controller
{
    public function index():InertiaResponse
    {
        $products = Product::with('category')
        ->where('is_active', true)
        ->where('stock', '>', 0)
        ->orderBy('name')
        ->get();

        return Inertia::render('pos/index', [
            'products' => $products
        ]);
    }
}
