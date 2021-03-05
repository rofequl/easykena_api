<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        return DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')
        ->join('addresses', 'addresses.id', '=', 'orders.shipping_address_id')
        ->join('regions', 'regions.id', '=', 'addresses.region_id')
         ->join('cities', 'cities.id', '=', 'addresses.city_id')
         ->join('areas', 'areas.id', '=', 'addresses.area_id')
         ->select('regions.name as region', 'cities.name as city', 'areas.name as area', 'addresses.*','orders.*','users.name','users.email')->get();
    }
    public function orders_by_user(Request $request)
    {
        return DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')
        ->join('addresses', 'addresses.id', '=', 'orders.shipping_address_id')
        ->join('regions', 'regions.id', '=', 'addresses.region_id')
         ->join('cities', 'cities.id', '=', 'addresses.city_id')
         ->join('areas', 'areas.id', '=', 'addresses.area_id')
         ->select('regions.name as region', 'cities.name as city', 'areas.name as area', 'addresses.*','orders.*','users.name','users.email')->where('orders.user_id',Auth::user()->id)->get();
    }

    public function store(Request $request)
    {
       // return (array)$request;
      //dd($request->all());
      // echo $userId;
      // exit;
        $this->validate($request, [
          'shipping_address_id'=>'required',
          'total'=>'required',
        ]);

        $orderId = DB::table('orders')->insertGetId([
                    'user_id' => Auth::user()->id,
                    'date'=>date('Y-m-d'),
                    'shipping_address_id'=>$request->shipping_address_id,
                    'total'=>$request->total,
                    'discount_amount'=>0,
                    ]);
        foreach ($request->items as $key => $value) {
             DB::table('order_items')->insert([
                      'order_id' => $orderId,
                      'product_id'=>$value['id'],
                      'quantity'=>$value['quantity'],
                      'price'=>$value['price'],
                      ]);
          // code...
        }
        // return (array)$insert;
        return (array)DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')
        // ->join('order_items', 'order_items.order_id', '=', 'orders.id')
        ->join('addresses', 'addresses.id', '=', 'orders.shipping_address_id')
        ->join('regions', 'regions.id', '=', 'addresses.region_id')
         ->join('cities', 'cities.id', '=', 'addresses.city_id')
         ->join('areas', 'areas.id', '=', 'addresses.area_id')
         ->select('regions.name as region', 'cities.name as city', 'areas.name as area', 'addresses.*','orders.*','users.*')->where('orders.id', $orderId)->first();
    }

    public function update(Request $request, $id)
    {


    }
    public function orders_status_change(Request $request, $id)
    {
      $update = DB::table('orders')
            ->where('id', $id)
           ->update([
             'status' => $request->status,
      ]);
      return (array)DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')
      ->join('addresses', 'addresses.id', '=', 'orders.shipping_address_id')
      ->join('regions', 'regions.id', '=', 'addresses.region_id')
       ->join('cities', 'cities.id', '=', 'addresses.city_id')
       ->join('areas', 'areas.id', '=', 'addresses.area_id')
       ->select('regions.name as region', 'cities.name as city', 'areas.name as area', 'addresses.*','orders.*','users.name','users.email')->where('orders.id', $id)->first();

    }
    public function orders_details($id){
      $order=DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')
      // ->join('order_items', 'order_items.order_id', '=', 'orders.id')
      ->join('addresses', 'addresses.id', '=', 'orders.shipping_address_id')
      ->join('regions', 'regions.id', '=', 'addresses.region_id')
       ->join('cities', 'cities.id', '=', 'addresses.city_id')
       ->join('areas', 'areas.id', '=', 'addresses.area_id')
       ->select('regions.name as region', 'cities.name as city', 'areas.name as area', 'addresses.*','orders.*','users.name','users.email')->where('orders.id', $id)->first();
     $orderItems = DB::table('order_items')
               ->join('products','products.id','order_items.product_id')
               ->select('order_items.*','products.name as item_name')
               ->where('order_id',$id)->get();
     return ['order'=>$order,'orderItems'=>$orderItems];
    }

    public function destroy($id)
    {
      $coupon = DB::table('orders')->where('id',$id)->delete();
      DB::table('order_items')->where('order_id',$id)->delete();
    }
}
