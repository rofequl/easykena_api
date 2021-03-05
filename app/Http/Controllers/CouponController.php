<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{

    public function index(Request $request)
    {
        return (array)DB::table('coupons')->get();
    }

    public function coupon_code_get($code)
    {
      // return $code;
        return (array)DB::table('coupons')->where('coupon_code',$code)->first();
    }
    public function store(Request $request)
    {

        $this->validate($request, [
            'coupon_code' => 'required|string|max:50',
            'discount_amount' => 'required',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $id = DB::table('coupons')->insertGetId([
                    'coupon_code' => $request->coupon_code,
                    'discount_amount'=>$request->discount_amount,
                    'date_from' =>$request->date_from,
                    'date_to' =>$request->date_to,
                    ]);

        return (array)DB::table('coupons')->where('coupons.id', $id)->first();
    }

    public function update(Request $request, $id)
    {

      $this->validate($request, [
          'coupon_code' => 'required|string|max:50',
          'discount_amount' => 'required',
          'date_from' => 'required|date',
          'date_to' => 'required|date',
      ]);

          $update = DB::table('coupons')
                ->where('id', $id)
               ->update([
                 'coupon_code' => $request->coupon_code,
                 'discount_amount'=>$request->discount_amount,
                 'date_from' =>$request->date_from,
                 'date_to' =>$request->date_to,
          ]);
          return (array)DB::table('coupons')->where('coupons.id', $id)->first();
    }

    public function destroy($id)
    {
        $coupon = DB::table('coupons')->where('id',$id)->delete();
        //File::delete(base_path('public/' . $brand->logo));
        // $coupon->delete();
    }
}
