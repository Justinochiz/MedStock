<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Mail\SendOrderStatus;

class OrderController extends Controller
{
    public function processOrder($id)
    {
        $customer = DB::table('customer as c')->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
            ->where('o.orderinfo_id', $id)
            ->select(
                'c.lname',
                'c.fname',
                'c.addressline',
                'c.phone',
                'o.orderinfo_id',
                'o.status',
                'o.date_placed',
                'o.discount_code',
                'o.discount_amount',
                'o.subtotal_amount',
                'o.total_amount',
                'o.shipping'
            )
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

        $receiptFilename = 'receipt-' . $id . '.pdf';
        $receiptPath = 'receipts/' . $receiptFilename;
        $receiptUrl = Storage::disk('public')->exists($receiptPath)
            ? Storage::url($receiptPath)
            : null;

        $subtotal = $customer->subtotal_amount !== null ? (float) $customer->subtotal_amount : (float) $total;
        $discountAmount = $customer->discount_amount !== null ? (float) $customer->discount_amount : 0.0;
        $grandTotal = $customer->total_amount !== null ? (float) $customer->total_amount : max(0, $subtotal - $discountAmount);

        return view('order.processOrder', compact('customer', 'orders', 'total', 'receiptUrl', 'subtotal', 'discountAmount', 'grandTotal'));
    }

    public function orderUpdate(Request $request, $id)
    {
        // dd($request);
        $updated = Order::where('orderinfo_id', $id)
            ->update(['status' => $request->status]);
        // dd($order);
        // dd($user->id);

        // dd($order > 0);
        if ($updated > 0) {
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

            $orderMeta = DB::table('orderinfo as o')
                ->join('customer as c', 'o.customer_id', '=', 'c.customer_id')
                ->where('o.orderinfo_id', $id)
                ->select(
                    'o.orderinfo_id',
                    'o.status',
                    'o.date_placed',
                    'o.total_amount',
                    'o.payment_method',
                    'c.fname',
                    'c.lname',
                    'c.phone',
                    'c.addressline',
                    'c.town',
                    'c.zipcode'
                )
                ->first();

            $user =  DB::table('users as u')
                ->join('customer as c', 'u.id', '=', 'c.user_id')
                ->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
                ->where('o.orderinfo_id', $id)
                ->select('u.id', 'u.email')
                ->first();
            // dd($user);

            $customerName = trim((string) (($orderMeta->fname ?? '') . ' ' . ($orderMeta->lname ?? '')));
            $shippingAddress = implode(', ', array_filter([
                $orderMeta->addressline,
                $orderMeta->town,
                $orderMeta->zipcode,
            ]));

            $receiptData = [
                'order_number' => $orderMeta->orderinfo_id ?? $id,
                'customer_name' => $customerName,
                'customer_email' => $user->email ?? null,
                'customer_phone' => $orderMeta->phone ?? null,
                'shipping_address' => $shippingAddress,
                'payment_method' => $orderMeta->payment_method ?? null,
                'status' => $orderMeta->status ?? $request->status,
                'date_placed' => $orderMeta->date_placed ?? null,
                'total_amount' => $orderMeta->total_amount ?? null,
            ];

            Mail::to($user->email)
                ->send(new SendOrderStatus($myOrder, $receiptData));
            return redirect()->route('admin.orders')->with('success', 'order updated');
        }

        // return redirect()->route('admin.orders')->with('success', 'order updated');
        return redirect()->route('admin.orders')->with('error', 'email not sent');
    }
}
