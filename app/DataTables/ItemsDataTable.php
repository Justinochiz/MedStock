<?php

namespace App\DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ItemsDataTable extends DataTable
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
                $storagePath = str_replace('public/', '', (string) $primaryImage);
                $url = !empty($storagePath) && Storage::disk('public')->exists($storagePath)
                    ? asset('storage/' . $storagePath)
                    : asset('images/medstock-logo.png');
                $countBadge = count($gallery) > 1
                    ? '<span class="badge bg-secondary ms-1">+' . (count($gallery) - 1) . '</span>'
                    : '';

                return '<img src="' . e($url) . '" alt="item image" width="50" height="50">' . $countBadge;
            })
            ->addColumn('action', function ($row) {
                if (!empty($row->deleted_at)) {
                    $restoreUrl = route('items.restore', $row->item_id);

                    return '<form action="' . e($restoreUrl) . '" method="POST" style="display:inline-block;">'
                        . csrf_field()
                        . method_field('PATCH')
                        . '<button type="submit" title="Restore" style="border:none;background:none;padding:0;cursor:pointer;">'
                        . '<i class="fa-solid fa-rotate-left" style="color:blue"></i>'
                        . '</button>'
                        . '</form>';
                }

                $editUrl = route('items.edit', $row->item_id);
                $deleteUrl = route('items.destroy', $row->item_id);

                $form = '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline-block;margin-left:8px;" onsubmit="return confirm(\'Are you sure you want to delete this item?\');">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '<button type="submit" style="border:none;background:none;padding:0;cursor:pointer;">'
                    . '<i class="fas fa-trash" style="color:red"></i>'
                    . '</button>'
                    . '</form>';

                return '<a href="' . e($editUrl) . '"><i class="fas fa-edit"></i></a>' . $form;
            })
            ->rawColumns(['image', 'action'])
            ->setRowId('item_id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query()
    {
        return DB::table('item as i')
            ->leftJoin('stock as s', 'i.item_id', '=', 's.item_id')
            ->select(
                'i.item_id',
                'i.description',
                'i.category',
                'i.sell_price',
                'i.cost_price',
                'i.img_path',
                'i.gallery_paths',
                'i.deleted_at',
                's.quantity as quantity'
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('items-table')
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
            Column::make('item_id')->title('Item ID'),
            Column::computed('image')->title('Image')->exportable(false)->printable(false),
            Column::make('description'),
            Column::make('category')->title('Category'),
            Column::make('sell_price')->title('Sell Price'),
            Column::make('cost_price')->title('Cost Price'),
            Column::make('quantity')->name('s.quantity')->title('Quantity'),
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
        return 'Items_' . date('YmdHis');
    }
}
