<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Retailer;

class RetailerController extends Controller
{
    public function index()
    {
        return response()->json(Retailer::all());
    }
}
