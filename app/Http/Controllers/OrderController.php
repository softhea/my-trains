<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        // Save order logic here (store in DB or email you)

        return back()->with(
            'success', 
        __('Order placed. We’ll contact you soon!')
        );
    }
}
