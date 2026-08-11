<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $promos = Promo::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        return response()->json($promos);
    }
}