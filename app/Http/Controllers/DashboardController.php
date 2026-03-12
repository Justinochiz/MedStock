<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CustomersDataTable;
use App\DataTables\UsersDataTable;
use App\DataTables\OrderDataTable;
use DB;
use App\Charts\CustomerChart;
use App\Charts\MonthlySalesChart;
use App\Charts\ItemChart;

class DashboardController extends Controller
{
    private $bgcolor;
    public function __construct()
    {

        $this->bgcolor = collect([
            '#7158e2',
            '#3ae374',
            '#ff3838',
            "#FF851B",
            "#7FDBFF",
            "#B10DC9",
            "#FFDC00",
            "#001f3f",
            "#39CCCC",
            "#01FF70",
            "#85144b",
            "#F012BE",
            "#3D9970",
            "#111111",
            "#AAAAAA",
        ]);
    }

    public function index()
    {

        // SELECT count(addressline), addressline from customer group by addressline;
        $customers = DB::table('customer')
            ->whereNotNull('addressline')
            // ->select(DB::raw('count(addressline) as total'), 'addressline')
            ->groupBy('addressline')
            ->orderBy('total', 'DESC')
            ->pluck(DB::raw('count(addressline) as total'), 'addressline')
            ->all();
        // dd($customers);
        $customerChart = new CustomerChart;
        $dataset = $customerChart->labels(array_keys($customers));
        // dd($dataset);
        $dataset = $customerChart->dataset(
            'Customer Demographics',
            'bar',
            array_values($customers)
        );
        $dataset = $dataset->backgroundColor($this->bgcolor);
        $customerChart->options([
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled' => true],
            'aspectRatio' => 1,
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => ['display' => false],
                        'display' => true,
                    ],
                ],
            ],
        ]);

        $orders = DB::table('orderinfo as o')
            ->join('orderline as ol', 'o.orderinfo_id', '=', 'ol.orderinfo_id')
            ->join('item as i', 'i.item_id', '=', 'ol.item_id')
            ->groupBy(DB::raw('month(o.date_placed)'))
            ->pluck(DB::raw('sum(i.sell_price * ol.quantity) AS total'), DB::raw('monthname(o.date_placed) as month'))
            ->all();
        // dd($orders);
        $salesChart = new MonthlySalesChart;
        $dataset = $salesChart->labels(array_keys($orders));
        $dataset = $salesChart->dataset(
            'Monthly sales 2026',
            'line',
            array_values($orders)
        );


        $salesChart->options([
            'indexAxis' => 'y',
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled' => true],
            'aspectRatio' => 1,
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => ['display' => false],
                        'display' => true,
                    ],
                ],
            ],
            'fill' => false,
            'borderColor' => 'blue'
        ]);

        $items = DB::table('orderline AS ol')
            ->join('item AS i', 'ol.item_id', '=', 'i.item_id')
            ->groupBy('i.description')
            ->orderBy('total', 'DESC')
            ->pluck(DB::raw('sum(ol.quantity) AS total'), 'description')
            ->all();
        // dd($items);

        $itemChart = new ItemChart;
        $dataset = $itemChart->labels(array_keys($items));
        // dd($dataset);
        $dataset = $itemChart->dataset(
            'Item sold',
            'doughnut',
            array_values($items)
        );

        $dataset = $dataset->backgroundColor($this->bgcolor);

        $dataset = $dataset->fill(false);
        $itemChart->options([
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled' => true],
            'aspectRatio' => 1,
        ]);
        return view('dashboard.index', compact('customerChart', 'salesChart', 'itemChart'));
    }
    public function getCustomers(CustomersDataTable $dataTable)
    {
        return $dataTable->render('dashboard.customers');
    }

    public function getUsers(UsersDataTable $dataTable)
    {
        return $dataTable->render('dashboard.users');
    }

    public function getOrders(OrderDataTable $dataTable)
    {
        return $dataTable->render('dashboard.orders');
    }
}
