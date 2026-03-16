<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('photo', function ($row) {
                $url = (!empty($row->photo_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($row->photo_path))
                    ? asset('storage/' . $row->photo_path)
                    : asset('images/medstock-logo.png');
                return '<img src="' . e($url) . '" alt="avatar" width="40" height="40" style="border-radius:50%;object-fit:cover;">';
            })
            ->addColumn('online', function ($row) {
                $isOnline = $row->last_seen_at && $row->last_seen_at->gt(now()->subMinutes(5));
                return $isOnline
                    ? '<span class="badge bg-success"><i class="fas fa-circle me-1" style="font-size:8px;"></i>Online</span>'
                    : '<span class="badge bg-secondary"><i class="fas fa-circle me-1" style="font-size:8px;"></i>Offline</span>';
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', 'users.action')
            ->rawColumns(['photo', 'online', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
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
            Column::make('id')->title('ID'),
            Column::computed('photo')->title('Photo')->exportable(false)->printable(false),
            Column::make('name'),
            Column::make('email'),
            Column::make('role'),
            Column::computed('online')->title('Online')->exportable(false)->printable(false),
            Column::computed('status')->title('Status')->exportable(false)->printable(false),
            Column::computed('action')->title('Role / Status')
                ->exportable(false)
                ->printable(false)
                ->width(220)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
