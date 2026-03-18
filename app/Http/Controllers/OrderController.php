<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Mail\SendOrderStatus;

class OrderController extends Controller
{
    public function processOrder($id)
    {
        $customer = DB::table('customer as c')->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
            ->where('o.orderinfo_id', $id)
            ->select('c.lname', 'c.fname', 'c.addressline', 'c.phone', 'o.orderinfo_id',  'o.status', 'o.date_placed')
            ->first();

        $itemOrders = DB::table('customer as c')
            ->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
            ->join('orderline as ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
            ->join('item as i', 'ol.item_id', '=', 'i.item_id')
            ->where('o.orderinfo_id', $id)
            ->select('i.description', 'ol.quantity', 'i.img_path', 'i.sell_price', DB::raw("'item' as line_type"))
            ->get();

        $serviceOrders = DB::table('customer as c')
            ->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
            ->join('service_orderline as sl', 'o.orderinfo_id', '=', 'sl.orderinfo_id')
            ->join('service as s', 'sl.service_id', '=', 's.service_id')
            ->where('o.orderinfo_id', $id)
            ->select('s.name as description', 'sl.quantity', 's.img_path', 's.price as sell_price', DB::raw("'service' as line_type"))
            ->get();

        $orders = $itemOrders->concat($serviceOrders)->values();

        $total = $orders->map(function ($item, $key) {
            return $item->sell_price * $item->quantity;
        })->sum();

        return view('order.processOrder', compact('customer', 'orders', 'total'));
    }

    public function orderUpdate(Request $request, $id)
    {
        // dd($request);
        $order = Order::where('orderinfo_id', $id)
            ->update(['status' => $request->status]);
        // dd($order);
        // dd($user->id);

        // dd($order > 0);
        if ($order > 0) {
            $itemLines = DB::table('customer as c')->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
                ->join('orderline as ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
                ->join('item as i', 'ol.item_id', '=', 'i.item_id')
                ->where('o.orderinfo_id', $id)
                ->select('c.user_id', 'i.description', 'ol.quantity', 'i.img_path', 'i.sell_price')
                ->get();

            $serviceLines = DB::table('customer as c')->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
                ->join('service_orderline as sl', 'o.orderinfo_id', '=', 'sl.orderinfo_id')
                ->join('service as s', 'sl.service_id', '=', 's.service_id')
                ->where('o.orderinfo_id', $id)
                ->select('c.user_id', 's.name as description', 'sl.quantity', 's.img_path', 's.price as sell_price')
                ->get();

            $myOrder = $itemLines->concat($serviceLines)->values();

            $user =  DB::table('users as u')
                ->join('customer as c', 'u.id', '=', 'c.user_id')
                ->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
                ->where('o.orderinfo_id', $id)
                ->select('u.id', 'u.email')
                ->first();
            // dd($user);
            Mail::to($user->email)
                ->send(new SendOrderStatus($myOrder));
            return redirect()->route('admin.orders')->with('success', 'order updated');
        }

        // return redirect()->route('admin.orders')->with('success', 'order updated');
        return redirect()->route('admin.orders')->with('error', 'email not sent');
    }
}
