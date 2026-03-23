<?php

namespace App\DataTables;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use DB;
use Illuminate\Support\Carbon;

class OrderDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable($query)
    {
        return datatables()->query($query)
            ->addColumn('action',  function ($row) {
                // $actionBtn = '<a href="#"  class="btn details btn-primary">Details</a>';
                $actionBtn = '<a href="' . route('admin.orderDetails', $row->orderinfo_id) . '"  class="btn details btn-primary">Details</a>';
                return $actionBtn;
            })
            ->editColumn('date_placed', function ($row) {
                return Carbon::parse($row->date_placed)
                    ->timezone(config('app.timezone', 'Asia/Manila'))
                    ->format('M d, Y h:i A');
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query()
    {
        $itemTotals = DB::table('orderline as ol')
            ->join('item as i', 'ol.item_id', '=', 'i.item_id')
            ->select('ol.orderinfo_id', DB::raw('SUM(ol.quantity * i.sell_price) as item_total'))
            ->groupBy('ol.orderinfo_id');

        $serviceTotals = DB::table('service_orderline as sl')
            ->join('service as s', 'sl.service_id', '=', 's.service_id')
            ->select('sl.orderinfo_id', DB::raw('SUM(sl.quantity * s.price) as service_total'))
            ->groupBy('sl.orderinfo_id');

        $orders = DB::table('customer as c')
            ->join('orderinfo as o', 'o.customer_id', '=', 'c.customer_id')
            ->leftJoinSub($itemTotals, 'it', function ($join) {
                $join->on('o.orderinfo_id', '=', 'it.orderinfo_id');
            })
            ->leftJoinSub($serviceTotals, 'st', function ($join) {
                $join->on('o.orderinfo_id', '=', 'st.orderinfo_id');
            })
            ->select(
                'o.orderinfo_id as orderinfo_id',
                'c.fname',
                'c.lname',
                'c.addressline',
                'o.date_placed',
                'o.status',
                DB::raw('COALESCE(NULLIF(o.total_amount, 0), (COALESCE(it.item_total, 0) + COALESCE(st.service_total, 0) - COALESCE(o.discount_amount, 0) + COALESCE(o.shipping, 0))) as total')
            );

        return $orders;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('orders-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['data' => 'orderinfo_id', 'name' => 'o.orderinfo_id', 'title' => 'order id'],
            ['data' => 'lname', 'name' => 'c.lname', 'title' => 'last name'],
            ['data' => 'fname', 'name' => 'c.fname', 'title' => 'first Name'],
            ['data' => 'addressline', 'name' => 'c.addressline', 'title' => 'address'],
            ['data' => 'date_placed', 'name' => 'o.date_placed', 'title' => 'date ordered'],
            ['data' => 'status', 'name' => 'o.status', 'title' => 'status'],
            Column::make('total')->searchable(false),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Order_' . date('YmdHis');
    }
}
