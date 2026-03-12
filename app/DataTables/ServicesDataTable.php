<?php

namespace App\DataTables;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ServicesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable($query)
    {
        return datatables()->query($query)
            ->addColumn('image', function ($row) {
                $gallery = json_decode($row->gallery_paths ?? '[]', true);
                if (!is_array($gallery)) {
                    $gallery = [];
                }

                $primaryImage = $gallery[0] ?? $row->img_path;
                if (empty($primaryImage)) {
                    return '<span class="text-muted">No image</span>';
                }

                $url = asset('storage/' . str_replace('public/', '', $primaryImage));
                $countBadge = count($gallery) > 1
                    ? '<span class="badge bg-secondary ms-1">+' . (count($gallery) - 1) . '</span>'
                    : '';

                return '<img src="' . e($url) . '" alt="service image" width="50" height="50">' . $countBadge;
            })
            ->addColumn('action', function ($row) {
                if (!empty($row->deleted_at)) {
                    $restoreUrl = route('services.restore', $row->service_id);

                    return '<form action="' . e($restoreUrl) . '" method="POST" style="display:inline-block;">'
                        . csrf_field()
                        . method_field('PATCH')
                        . '<button type="submit" title="Restore" style="border:none;background:none;padding:0;cursor:pointer;">'
                        . '<i class="fa-solid fa-rotate-left" style="color:blue"></i>'
                        . '</button>'
                        . '</form>';
                }

                $editUrl = route('services.edit', $row->service_id);
                $deleteUrl = route('services.destroy', $row->service_id);

                $form = '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline-block;margin-left:8px;">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '<button type="submit" style="border:none;background:none;padding:0;cursor:pointer;">'
                    . '<i class="fas fa-trash" style="color:red"></i>'
                    . '</button>'
                    . '</form>';

                return '<a href="' . e($editUrl) . '"><i class="fas fa-edit"></i></a>' . $form;
            })
            ->rawColumns(['image', 'action'])
            ->setRowId('service_id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query()
    {
        return DB::table('service as s')
            ->select(
                's.service_id',
                's.name',
                's.description',
                's.price',
                's.img_path',
                's.gallery_paths',
                's.deleted_at'
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('services-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
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
            Column::make('service_id')->title('Service ID'),
            Column::computed('image')->title('Image')->exportable(false)->printable(false),
            Column::make('name'),
            Column::make('description'),
            Column::make('price'),
            Column::make('deleted_at')->title('Deleted At')->defaultContent('-'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(90)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Services_' . date('YmdHis');
    }
}
