<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL()
        ], 200);
    }

    public function phone_number($number)
    {
        $length = strlen($number);
        if ($length === 11 && stripos($number, "+88") == false) return "+88" . $number;
        elseif ($length === 14 && stripos($number, "88") && preg_match('/^\+?\d+$/', $number)) return $number;
        else return false;
    }
}
