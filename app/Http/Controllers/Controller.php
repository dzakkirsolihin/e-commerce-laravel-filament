<?php

namespace App\Http\Controllers;

use App\Models\Product;

abstract class Controller
{
    //
    
    public function index()
    {
        $products = Product::all(); // Ambil semua produk dari database
        return view('welcome', compact('products'));
    }
}
