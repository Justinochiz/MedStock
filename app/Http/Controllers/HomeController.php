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
            ->where('c.user_id', Auth::id())
            ->select(
                'o.orderinfo_id',
                'o.date_placed',
                'o.status',
                'o.payment_method',
                'o.discount_code',
                'o.discount_amount',
                DB::raw('COALESCE(NULLIF(o.subtotal_amount, 0),
                    COALESCE((SELECT SUM(ol.quantity * i.sell_price)
                        FROM orderline ol
                        JOIN item i ON i.item_id = ol.item_id
                        WHERE ol.orderinfo_id = o.orderinfo_id), 0)
                    +
                    COALESCE((SELECT SUM(sol.quantity * s.price)
                        FROM service_orderline sol
                        JOIN service s ON s.service_id = sol.service_id
                        WHERE sol.orderinfo_id = o.orderinfo_id), 0)
                ) as subtotal_amount'),
                DB::raw('COALESCE(NULLIF(o.total_amount, 0),
                    COALESCE(NULLIF(o.subtotal_amount, 0),
                        COALESCE((SELECT SUM(ol.quantity * i.sell_price)
                            FROM orderline ol
                            JOIN item i ON i.item_id = ol.item_id
                            WHERE ol.orderinfo_id = o.orderinfo_id), 0)
                        +
                        COALESCE((SELECT SUM(sol.quantity * s.price)
                            FROM service_orderline sol
                            JOIN service s ON s.service_id = sol.service_id
                            WHERE sol.orderinfo_id = o.orderinfo_id), 0)
                    ) - COALESCE(o.discount_amount, 0)
                ) as total_amount'),
                DB::raw('COALESCE((SELECT SUM(ol.quantity) FROM orderline ol WHERE ol.orderinfo_id = o.orderinfo_id), 0)
                    + COALESCE((SELECT SUM(sol.quantity) FROM service_orderline sol WHERE sol.orderinfo_id = o.orderinfo_id), 0)
                    as total_qty')
            )
            ->orderByDesc('o.date_placed')
            ->get();

        $productOrderItems = DB::table('orderinfo as o')
            ->join('customer as c', 'o.customer_id', '=', 'c.customer_id')
            ->join('orderline as ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
            ->join('item as i', 'ol.item_id', '=', 'i.item_id')
            ->where('c.user_id', Auth::id())
            ->select(
                'o.orderinfo_id',
                'i.item_id',
                DB::raw('NULL as service_id'),
                'i.description as item_name',
                'i.img_path',
                'i.gallery_paths',
                'i.sell_price as unit_price',
                'ol.quantity',
                DB::raw("'Product' as line_type")
            )
            ->get();

        $serviceOrderItems = DB::table('orderinfo as o')
            ->join('customer as c', 'o.customer_id', '=', 'c.customer_id')
            ->join('service_orderline as sol', 'o.orderinfo_id', '=', 'sol.orderinfo_id')
            ->join('service as s', 'sol.service_id', '=', 's.service_id')
            ->where('c.user_id', Auth::id())
            ->select(
                'o.orderinfo_id',
                DB::raw('NULL as item_id'),
                's.service_id',
                's.name as item_name',
                's.img_path',
                's.gallery_paths',
                's.price as unit_price',
                'sol.quantity',
                DB::raw("'Service' as line_type")
            )
            ->get();

        $orderItems = $productOrderItems
            ->merge($serviceOrderItems)
            ->sortByDesc('orderinfo_id')
            ->groupBy('orderinfo_id');

        return view('home', compact('orders', 'orderItems'));
    }
}
