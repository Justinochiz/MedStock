@extends('layouts.base')
@section('body')
    <div id="items" class="container-fluid py-4 medical-items-page">
        @include('layouts.flash-messages')
        <div class="medical-page-header mb-4">
            <div>
                <h2 class="medical-title mb-1">
                    <i class="fas fa-clinic-medical me-2"></i>Medical Inventory
                </h2>
                <p class="medical-subtitle mb-0">Manage medical equipment, prices, and stock levels.</p>
            </div>
            <a class="btn medical-add-btn" href="{{ route('items.create') }}" role="button">
                <i class="fas fa-plus me-2"></i>Add Item
            </a>
        </div>

        <div class="medical-import-card mb-4">
            <form method="POST" enctype="multipart/form-data" action="{{ route('item.import') }}" class="medical-import-form">
                @csrf
                <div class="import-label-wrap">
                    <i class="fas fa-file-medical me-2"></i>
                    <label for="uploadName" class="mb-0">Import Excel Inventory</label>
                </div>
                <input type="file" id="uploadName" name="item_upload" class="form-control medical-file-input" required>
                <button type="submit" class="btn medical-import-btn">
                    <i class="fas fa-file-import me-2"></i>Import Excel File
                </button>
            </form>
        </div>

        <div class="medical-table-card">
            {{ $dataTable->table() }}
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
        <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
        <script src="/vendor/datatables/buttons.server-side.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableElement = $('#items-table');

                if (!tableElement.length) {
                    return;
                }

                tableElement.on('init.dt', function () {
                    const table = tableElement.DataTable();
                    const categoryColumnIndex = 3;
                    const filterWrapper = $('#items-table_filter');

                    if (!filterWrapper.length || filterWrapper.find('#category-filter').length) {
                        return;
                    }

                    const select = $('<select id="category-filter" class="form-select form-select-sm medical-category-filter"><option value="">All Categories</option></select>');

                    const loadCategories = function () {
                        const currentValue = select.val();
                        const uniqueCategories = [];

                        table.column(categoryColumnIndex, { search: 'applied' }).data().each(function (value) {
                            const textValue = String(value || '').trim();

                            if (textValue !== '' && !uniqueCategories.includes(textValue)) {
                                uniqueCategories.push(textValue);
                            }
                        });

                        uniqueCategories.sort(function (a, b) {
                            return a.localeCompare(b);
                        });

                        select.find('option:not(:first)').remove();

                        uniqueCategories.forEach(function (category) {
                            select.append($('<option></option>').val(category).text(category));
                        });

                        if (currentValue && uniqueCategories.includes(currentValue)) {
                            select.val(currentValue);
                        }
                    };

                    select.on('change', function () {
                        const selected = $(this).val();
                        const escaped = $.fn.dataTable.util.escapeRegex(selected);
                        table.column(categoryColumnIndex).search(selected ? '^' + escaped + '$' : '', true, false).draw();
                    });

                    filterWrapper.addClass('medical-filter-row');
                    filterWrapper.prepend(select);

                    loadCategories();
                    table.on('draw.dt', loadCategories);
                });
            });
        </script>
        <style>
            .medical-items-page {
                background: linear-gradient(135deg, #f8f9fb 0%, #f4f6fa 100%);
                min-height: 100vh;
            }

            .medical-page-header {
                background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
                border-left: 5px solid #4caf50;
                border-radius: 12px;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 24px;
                box-shadow: 0 6px 16px rgba(0, 102, 204, 0.15);
            }

            .medical-title {
                font-weight: 700;
                font-size: 1.75rem;
            }

            .medical-subtitle {
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.95rem;
            }

            .medical-add-btn {
                background: #4caf50;
                border: none;
                color: #fff;
                font-weight: 600;
                border-radius: 10px;
                padding: 10px 16px;
                box-shadow: 0 4px 10px rgba(76, 175, 80, 0.25);
            }

            .medical-add-btn:hover {
                background: #3f9a43;
                color: #fff;
            }

            .medical-import-card,
            .medical-table-card {
                background: #fff;
                border: 1px solid #e6edf5;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(16, 24, 40, 0.05);
            }

            .medical-import-form {
                padding: 18px;
                display: grid;
                grid-template-columns: 1fr 2fr auto;
                gap: 12px;
                align-items: center;
            }

            .import-label-wrap {
                display: flex;
                align-items: center;
                font-weight: 600;
                color: #1f4b7f;
                font-size: 0.95rem;
            }

            .medical-file-input {
                border-radius: 10px;
                border: 1px solid #cfd9e6;
            }

            .medical-import-btn {
                background: linear-gradient(135deg, #00a7d1 0%, #008fb3 100%);
                border: none;
                color: #fff;
                border-radius: 10px;
                font-weight: 600;
                white-space: nowrap;
                padding: 10px 16px;
            }

            .medical-import-btn:hover {
                color: #fff;
                filter: brightness(0.95);
            }

            .medical-table-card {
                padding: 14px;
                overflow-x: auto;
            }

            .medical-table-card table.dataTable {
                min-width: 1100px;
                font-size: 0.92rem;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 12px;
                color: #1f4b7f;
                font-weight: 600;
            }

            .dataTables_wrapper .medical-filter-row {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                flex-wrap: wrap;
            }

            .dataTables_wrapper .medical-category-filter {
                width: 200px;
                border: 1px solid #cdd8e6;
                border-radius: 8px;
                padding: 6px 10px;
                background-color: #fff;
                color: #1f4b7f;
                font-weight: 600;
            }

            .dataTables_wrapper .dataTables_filter input,
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #cdd8e6;
                border-radius: 8px;
                padding: 6px 10px;
                margin-left: 6px;
                background-color: #fff;
            }

            table.dataTable thead th {
                background: #f2f7fc;
                color: #1f4b7f;
                border-bottom: 2px solid #d8e4f2;
                font-weight: 700;
                font-size: 0.9rem;
                white-space: nowrap;
            }

            table.dataTable tbody td {
                font-size: 0.92rem;
                white-space: nowrap;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                font-size: 0.9rem;
            }

            table.dataTable tbody tr:hover {
                background-color: #f7fbff;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current,
            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
                color: #fff !important;
                border: 1px solid #0057b0;
                border-radius: 8px;
            }

            @media (max-width: 991px) {
                .medical-import-form {
                    grid-template-columns: 1fr;
                }

                .medical-page-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .medical-add-btn {
                    width: 100%;
                }

                .medical-table-card table.dataTable {
                    font-size: 0.86rem;
                }

                .dataTables_wrapper .medical-filter-row {
                    justify-content: flex-start;
                }

                .dataTables_wrapper .medical-category-filter {
                    width: 100%;
                }
            }
        </style>
        {!! $dataTable->scripts() !!}
    @endpush
@endsection
