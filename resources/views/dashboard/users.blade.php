@extends('layouts.base')

@section('body')
    @include('layouts.flash-messages')

    <div class="container-fluid py-4 medical-users-page">
        <div class="medical-page-header mb-4">
            <div>
                <h2 class="medical-title mb-1">
                    <i class="fas fa-user-shield me-2"></i>Users & Staff
                </h2>
                <p class="medical-subtitle mb-0">Manage admin and user accounts for the medical shop system.</p>
            </div>
            <div class="welcome-chip">
                <i class="fas fa-user-nurse me-2"></i>{{ Auth::check() ? Auth::user()->name : '' }}
            </div>
        </div>

        <div class="medical-table-card">
            {{ $dataTable->table() }}
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    <style>
        .medical-users-page {
            background: linear-gradient(135deg, #f8f9fb 0%, #f4f6fa 100%);
            min-height: 100vh;
        }

        .medical-page-header {
            background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
            border-left: 5px solid #4caf50;
            border-radius: 12px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 22px 24px;
            box-shadow: 0 6px 16px rgba(0, 102, 204, 0.16);
        }

        .medical-title {
            font-weight: 700;
            font-size: 1.8rem;
        }

        .medical-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }

        .welcome-chip {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
            white-space: nowrap;
        }

        .medical-table-card {
            background: #fff;
            border: 1px solid #e5edf6;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(16, 24, 40, 0.05);
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

        .btn-primary,
        .btn-info {
            background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover,
        .btn-info:hover {
            filter: brightness(0.95);
        }

        @media (max-width: 991px) {
            .medical-page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .medical-table-card table.dataTable {
                font-size: 0.86rem;
            }
        }
    </style>
    {!! $dataTable->scripts() !!}
@endpush