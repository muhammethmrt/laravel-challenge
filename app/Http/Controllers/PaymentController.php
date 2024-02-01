<?php

namespace App\Http\Controllers;

class PaymentController extends Controller
{

    public function pay()
    {
        
        return response()->json(['message' => 'success'], 200);

    }
}
