<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = DB::table('orderinfo as o')
            ->join('customer as c', 'o.customer_id', '=', 'c.customer_id')
            ->leftJoin('orderline as ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
            ->leftJoin('item as i', 'ol.item_id', '=', 'i.item_id')
            ->where('c.user_id', Auth::id())
            ->groupBy(
                'o.orderinfo_id',
                'o.date_placed',
                'o.status',
                'o.payment_method',
                'o.discount_code',
                'o.discount_amount',
                'o.subtotal_amount',
                'o.total_amount'
            )
            ->select(
                'o.orderinfo_id',
                'o.date_placed',
                'o.status',
                'o.payment_method',
                'o.discount_code',
                'o.discount_amount',
                DB::raw('COALESCE(NULLIF(o.subtotal_amount, 0), SUM(ol.quantity * i.sell_price), 0) as subtotal_amount'),
                DB::raw('COALESCE(NULLIF(o.total_amount, 0), COALESCE(NULLIF(o.subtotal_amount, 0), SUM(ol.quantity * i.sell_price), 0) - COALESCE(o.discount_amount, 0), 0) as total_amount'),
                DB::raw('COALESCE(SUM(ol.quantity), 0) as total_qty')
            )
            ->orderByDesc('o.date_placed')
            ->get();

        $orderItems = DB::table('orderinfo as o')
            ->join('customer as c', 'o.customer_id', '=', 'c.customer_id')
            ->join('orderline as ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
            ->join('item as i', 'ol.item_id', '=', 'i.item_id')
            ->where('c.user_id', Auth::id())
            ->select(
                'o.orderinfo_id',
                'i.description',
                'i.img_path',
                'i.gallery_paths',
                'i.sell_price',
                'ol.quantity'
            )
            ->orderByDesc('o.date_placed')
            ->get()
            ->groupBy('orderinfo_id');

        return view('home', compact('orders', 'orderItems'));
    }
}
