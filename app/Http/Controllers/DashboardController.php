<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CustomersDataTable;
use App\DataTables\UsersDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\ReviewDataTable;
use DB;
use App\Charts\CustomerChart;
use App\Charts\MonthlySalesChart;
use App\Charts\ItemChart;
use App\Charts\YearlySalesChart;
use App\Charts\WeeklySalesChart;
use Carbon\Carbon;

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

    public function index(Request $request)
    {
        $dateFrom = null;
        $dateTo = null;

        try {
            if ($request->filled('date_from')) {
                $dateFrom = Carbon::parse($request->query('date_from'))->startOfDay();
            }
        } catch (\Throwable $e) {
            $dateFrom = null;
        }

        try {
            if ($request->filled('date_to')) {
                $dateTo = Carbon::parse($request->query('date_to'))->endOfDay();
            }
        } catch (\Throwable $e) {
            $dateTo = null;
        }

        if ($dateFrom && $dateTo && $dateFrom->gt($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        }

        $dateFromValue = $dateFrom ? $dateFrom->toDateString() : '';
        $dateToValue = $dateTo ? $dateTo->toDateString() : '';

        $applyOrderDateRange = function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $query->where('o.date_placed', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('o.date_placed', '<=', $dateTo);
            }

            return $query;
        };

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
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'fontColor' => '#38506b',
                    'boxWidth' => 28,
                    'fontStyle' => '600',
                ],
            ],
            'tooltips' => [
                'enabled' => true,
                'backgroundColor' => 'rgba(20, 30, 48, 0.92)',
                'titleFontStyle' => '600',
                'bodyFontStyle' => '600',
            ],
            'layout' => [
                'padding' => [
                    'top' => 8,
                    'right' => 10,
                    'bottom' => 2,
                    'left' => 2,
                ],
            ],
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                            'precision' => 0,
                        ],
                        'gridLines' => [
                            'color' => 'rgba(44, 90, 160, 0.10)',
                            'drawBorder' => false,
                            'zeroLineColor' => 'rgba(44, 90, 160, 0.18)',
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => [
                            'display' => false,
                            'drawBorder' => false,
                        ],
                        'display' => true,
                        'ticks' => [
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                    ],
                ],
            ],
        ]);

        $orderAmountExpression = "COALESCE(\n"
            . "NULLIF(o.total_amount, 0),\n"
            . "NULLIF(o.subtotal_amount, 0),\n"
            . "COALESCE((SELECT SUM(ol.quantity * i.sell_price)\n"
            . "    FROM orderline ol\n"
            . "    JOIN item i ON i.item_id = ol.item_id\n"
            . "    WHERE ol.orderinfo_id = o.orderinfo_id), 0)\n"
            . "+\n"
            . "COALESCE((SELECT SUM(sol.quantity * s.price)\n"
            . "    FROM service_orderline sol\n"
            . "    JOIN service s ON s.service_id = sol.service_id\n"
            . "    WHERE sol.orderinfo_id = o.orderinfo_id), 0),\n"
            . "0\n"
            . ")";

        $prescriptionsFilled = (int) DB::table('orderline')->sum('quantity');
        $totalRevenue = (float) (DB::table('orderinfo as o')
            ->selectRaw("SUM({$orderAmountExpression}) as total_revenue")
            ->value('total_revenue') ?? 0);
        $activePatients = (int) DB::table('customer')->count();
        $medicineStock = (int) DB::table('stock')->sum('quantity');

        $weeklyOrdersQuery = DB::table('orderinfo as o')
            ->whereNotNull('o.date_placed');

        $applyOrderDateRange($weeklyOrdersQuery);

        $weeklyOrders = $weeklyOrdersQuery
            ->groupBy(DB::raw('yearweek(o.date_placed, 1)'))
            ->selectRaw("date_format(min(o.date_placed), '%x-W%v') as period, sum({$orderAmountExpression}) as total")
            ->orderBy(DB::raw('yearweek(o.date_placed, 1)'))
            ->pluck('total', 'period')
            ->all();

        if (empty($weeklyOrders)) {
            $weeklyOrders = ['No Data' => 0];
        }

        $weeklySalesChart = new WeeklySalesChart;
        $weeklySalesChart->labels(array_keys($weeklyOrders));
        $weeklySalesChart->dataset(
            'Weekly sales',
            'bar',
            array_values($weeklyOrders)
        )->backgroundColor('rgba(40, 167, 69, 0.62)')->color('#28a745');

        $weeklySalesChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'fontColor' => '#38506b',
                    'boxWidth' => 28,
                    'fontStyle' => '600',
                ],
            ],
            'tooltips' => [
                'enabled' => true,
                'backgroundColor' => 'rgba(20, 30, 48, 0.92)',
                'titleFontStyle' => '600',
                'bodyFontStyle' => '600',
            ],
            'layout' => [
                'padding' => [
                    'top' => 8,
                    'right' => 10,
                    'bottom' => 2,
                    'left' => 2,
                ],
            ],
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                        'gridLines' => [
                            'color' => 'rgba(44, 90, 160, 0.10)',
                            'drawBorder' => false,
                            'zeroLineColor' => 'rgba(44, 90, 160, 0.18)',
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => [
                            'display' => false,
                            'drawBorder' => false,
                        ],
                        'display' => true,
                        'ticks' => [
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                        'barPercentage' => 0.55,
                        'categoryPercentage' => 0.7,
                    ],
                ],
            ],
        ]);

        $monthlyOrdersQuery = DB::table('orderinfo as o')
            ->whereNotNull('o.date_placed');

        $applyOrderDateRange($monthlyOrdersQuery);

        $monthlyOrders = $monthlyOrdersQuery
            ->groupBy(DB::raw('year(o.date_placed)'), DB::raw('month(o.date_placed)'))
            ->selectRaw("date_format(min(o.date_placed), '%Y-%m') as period, sum({$orderAmountExpression}) as total")
            ->orderBy(DB::raw('year(o.date_placed)'))
            ->orderBy(DB::raw('month(o.date_placed)'))
            ->pluck('total', 'period')
            ->all();

        if (empty($monthlyOrders)) {
            $monthlyOrders = ['No Data' => 0];
        }

        $salesChart = new MonthlySalesChart;
        $dataset = $salesChart->labels(array_keys($monthlyOrders));
        $dataset = $salesChart->dataset(
            'Monthly sales',
            'bar',
            array_values($monthlyOrders)
        );

        $dataset = $dataset->backgroundColor('rgba(0, 102, 204, 0.60)');


        $salesChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'fontColor' => '#38506b',
                    'boxWidth' => 28,
                    'fontStyle' => '600',
                ],
            ],
            'tooltips' => [
                'enabled' => true,
                'backgroundColor' => 'rgba(20, 30, 48, 0.92)',
                'titleFontStyle' => '600',
                'bodyFontStyle' => '600',
            ],
            'layout' => [
                'padding' => [
                    'top' => 8,
                    'right' => 10,
                    'bottom' => 2,
                    'left' => 2,
                ],
            ],
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                        'gridLines' => [
                            'color' => 'rgba(44, 90, 160, 0.10)',
                            'drawBorder' => false,
                            'zeroLineColor' => 'rgba(44, 90, 160, 0.18)',
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => [
                            'display' => false,
                            'drawBorder' => false,
                        ],
                        'display' => true,
                        'ticks' => [
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                        'barPercentage' => 0.55,
                        'categoryPercentage' => 0.7,
                    ],
                ],
            ],
        ]);

        $yearlyOrdersQuery = DB::table('orderinfo as o')
            ->whereNotNull('o.date_placed');

        $applyOrderDateRange($yearlyOrdersQuery);

        $yearlyOrders = $yearlyOrdersQuery
            ->groupBy(DB::raw('year(o.date_placed)'))
            ->selectRaw("year(o.date_placed) as sales_year, sum({$orderAmountExpression}) as total")
            ->orderBy('sales_year')
            ->pluck('total', 'sales_year')
            ->all();

        if (empty($yearlyOrders)) {
            $yearlyOrders = ['No Data' => 0];
        }

        $yearlySalesChart = new YearlySalesChart;
        $yearlySalesChart->labels(array_map('strval', array_keys($yearlyOrders)));
        $yearlySalesChart->dataset(
            'Yearly sales',
            'bar',
            array_values($yearlyOrders)
        )->backgroundColor('rgba(23, 162, 184, 0.65)');

        $yearlySalesChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'fontColor' => '#38506b',
                    'boxWidth' => 28,
                    'fontStyle' => '600',
                ],
            ],
            'tooltips' => [
                'enabled' => true,
                'backgroundColor' => 'rgba(20, 30, 48, 0.92)',
                'titleFontStyle' => '600',
                'bodyFontStyle' => '600',
            ],
            'layout' => [
                'padding' => [
                    'top' => 8,
                    'right' => 10,
                    'bottom' => 2,
                    'left' => 2,
                ],
            ],
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                        'gridLines' => [
                            'color' => 'rgba(44, 90, 160, 0.10)',
                            'drawBorder' => false,
                            'zeroLineColor' => 'rgba(44, 90, 160, 0.18)',
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => [
                            'display' => false,
                            'drawBorder' => false,
                        ],
                        'display' => true,
                        'ticks' => [
                            'fontColor' => '#5c708a',
                            'fontStyle' => '600',
                        ],
                        'barPercentage' => 0.45,
                        'categoryPercentage' => 0.58,
                    ],
                ],
            ],
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
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'fontColor' => '#38506b',
                    'boxWidth' => 26,
                    'fontStyle' => '600',
                ],
            ],
            'tooltips' => [
                'enabled' => true,
                'backgroundColor' => 'rgba(20, 30, 48, 0.92)',
                'titleFontStyle' => '600',
                'bodyFontStyle' => '600',
            ],
            'layout' => [
                'padding' => [
                    'top' => 6,
                    'right' => 8,
                    'bottom' => 4,
                    'left' => 8,
                ],
            ],
        ]);
        return view('dashboard.index', compact(
            'customerChart',
            'weeklySalesChart',
            'salesChart',
            'yearlySalesChart',
            'itemChart',
            'prescriptionsFilled',
            'totalRevenue',
            'activePatients',
            'medicineStock',
            'dateFromValue',
            'dateToValue'
        ));
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

    public function getReviews(ReviewDataTable $dataTable)
    {
        return $dataTable->render('dashboard.reviews');
    }

    public function discountCodes()
    {
        $discountCodes = [
            ['code' => 'MEDSAVE5', 'percent' => 5],
            ['code' => 'CARE10', 'percent' => 10],
            ['code' => 'HEALTH15', 'percent' => 15],
            ['code' => 'WELL20', 'percent' => 20],
            ['code' => 'IMED25', 'percent' => 25],
        ];

        return view('dashboard.discount-codes', compact('discountCodes'));
    }
}
