<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('loyaltyTransactions.order');
        $rate = (float) Setting::getValue('points_earning_rate', 1);
        $redeemRate = (float) Setting::getValue('points_redeem_rate', 0.10);
        $minPoints = (int) Setting::getValue('min_points_to_redeem', 100);

        return view('loyalty.index', compact('user', 'rate', 'redeemRate', 'minPoints'));
    }
}
