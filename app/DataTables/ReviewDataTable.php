<?php

namespace App\DataTables;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReviewDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable($query)
    {
        return datatables()->query($query)
            ->addColumn('action', function ($row) {
                $deleteUrl = route('admin.reviews.destroy', $row->id);
                $csrf = csrf_token();

                return '<form method="POST" action="' . e($deleteUrl) . '" style="display:inline;" '
                    . 'onsubmit="return confirm(\'Delete this review?\')">'
                    . '<input type="hidden" name="_token" value="' . e($csrf) . '">'
                    . '<input type="hidden" name="_method" value="DELETE">'
                    . '<button type="submit" class="btn btn-sm btn-danger">Delete</button>'
                    . '</form>';
            })
            ->editColumn('rating', function ($row) {
                $rating = max(1, min(5, (int) $row->rating));
                $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

                return '<span class="text-warning" style="letter-spacing:1px;">' . $stars . '</span> '
                    . '<span class="text-muted">(' . $rating . '/5)</span>';
            })
            ->editColumn('comment', function ($row) {
                $comment = trim((string) ($row->comment ?? ''));

                return $comment === '' ? 'No comment' : e(Str::limit($comment, 120));
            })
            ->editColumn('verified_purchase', function ($row) {
                return (bool) $row->verified_purchase
                    ? '<span class="badge bg-success">Verified</span>'
                    : '<span class="badge bg-secondary">Unverified</span>';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at, 'UTC')
                    ->setTimezone(config('app.timezone', 'Asia/Manila'))
                    ->format('M d, Y h:i A');
            })
            ->rawColumns(['rating', 'verified_purchase', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query()
    {
        return DB::table('reviews as r')
            ->join('users as u', 'u.id', '=', 'r.user_id')
            ->leftJoin('item as i', 'i.item_id', '=', 'r.item_id')
            ->leftJoin('service as s', 's.service_id', '=', 'r.service_id')
            ->select(
                'r.id',
                'u.name as reviewer_name',
                'u.email as reviewer_email',
                DB::raw("CASE WHEN r.item_id IS NOT NULL THEN 'Product' ELSE 'Service' END as target_type"),
                DB::raw('COALESCE(i.description, s.name) as target_name'),
                'r.rating',
                'r.comment',
                'r.verified_purchase',
                'r.created_at'
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reviews-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('reviewer_name')->title('Reviewer'),
            Column::make('reviewer_email')->title('Email'),
            Column::make('target_type')->title('Type'),
            Column::make('target_name')->title('Product / Service'),
            Column::make('rating')->title('Rating')->searchable(false),
            Column::make('comment')->title('Comment')->searchable(false),
            Column::make('verified_purchase')->title('Purchase')->searchable(false),
            Column::make('created_at')->title('Submitted'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Reviews_' . date('YmdHis');
    }
}