@extends('layouts.base')
@section('title')
    Admin Dashboard - iMedStock
@endsection
@section('body')
    @include('layouts.flash-messages')

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2">
                    <i class="fas fa-chart-line"></i> Dashboard
                </h1>
                <p class="text-muted">Welcome back, <strong>{{ Auth::user()->name }}</strong>!</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text text-muted mb-1">Total Orders</p>
                                <h3 class="card-title mb-0">0</h3>
                            </div>
                            <div style="font-size: 2rem; color: #667eea;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text text-muted mb-1">Total Revenue</p>
                                <h3 class="card-title mb-0">$0</h3>
                            </div>
                            <div style="font-size: 2rem; color: #28a745;">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text text-muted mb-1">Customers</p>
                                <h3 class="card-title mb-0">0</h3>
                            </div>
                            <div style="font-size: 2rem; color: #17a2b8;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text text-muted mb-1">Equipment</p>
                                <h3 class="card-title mb-0">0</h3>
                            </div>
                            <div style="font-size: 2rem; color: #ffc107;">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i> Monthly Sales
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $salesChart->container() !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-pie-chart"></i> Top Products
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center py-4">No data available yet</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users-chart"></i> Customer Analytics
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $customerChart->container() !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i> Items Sold
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $itemChart->container() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .card-title {
            color: #333;
            font-weight: 600;
        }

        .card-header {
            border-bottom: none;
        }

        h1 {
            color: #333;
            font-weight: 700;
        }
    </style>

    {!! $salesChart->script() !!}
    {!! $customerChart->script() !!}
    {!! $itemChart->script() !!}
@endsection
