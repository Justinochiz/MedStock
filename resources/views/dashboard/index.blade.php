@extends('layouts.base')
@section('title')
    Admin Dashboard - iMedStock
@endsection
@section('body')
    @include('layouts.flash-messages')

    <div class="container-fluid py-4 medical-dashboard">
        <div class="row mb-4">
            <div class="col-12">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">
                        <i class="fas fa-stethoscope"></i> Medical Shop Dashboard
                    </h1>
                    <p class="dashboard-subtitle">Welcome back, <strong>{{ Auth::user()->name }}</strong>! — Manage your medical inventory and orders</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards - Medical Theme -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card medical-card border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text stat-label">Prescriptions Filled</p>
                                <h3 class="card-title stat-number">0</h3>
                            </div>
                            <div class="stat-icon" style="color: #0066CC;">
                                <i class="fas fa-file-prescription"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card medical-card border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text stat-label">Revenue Generated</p>
                                <h3 class="card-title stat-number">$0</h3>
                            </div>
                            <div class="stat-icon" style="color: #4CAF50;">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card medical-card border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text stat-label">Active Patients</p>
                                <h3 class="card-title stat-number">0</h3>
                            </div>
                            <div class="stat-icon" style="color: #17a2b8;">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card medical-card border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text stat-label">Medicine Stock</p>
                                <h3 class="card-title stat-number">0</h3>
                            </div>
                            <div class="stat-icon" style="color: #ffc107;">
                                <i class="fas fa-pills"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Charts Section -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card medical-card border-0">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i> Monthly Prescription Sales Trends
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $salesChart->container() !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card medical-card border-0">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-capsules"></i> Best Selling Medicines
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
                <div class="card medical-card border-0">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-injured"></i> Patient Analytics & Demographics
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $customerChart->container() !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card medical-card border-0">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-vial"></i> Medicine Sales Distribution
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
        .medical-dashboard {
            background: linear-gradient(135deg, #f8f9fb 0%, #f4f6fa 100%);
            min-height: 100vh;
            padding: 30px 15px;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #0066CC 0%, #004494 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.15);
            border-left: 5px solid #4CAF50;
        }

        .dashboard-title {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-subtitle {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0 32px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .medical-card {
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid #e8ecf4;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .medical-card:hover {
            box-shadow: 0 8px 24px rgba(0, 102, 204, 0.12);
            transform: translateY(-2px);
            border-color: #b3d9ff;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            color: #0066CC;
            font-weight: 700;
            font-size: 2rem;
            margin: 0;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            transition: transform 0.3s ease;
        }

        .medical-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .card-title {
            color: #0066CC;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .card-header {
            border-bottom: 1px solid #e8ecf4;
            background: linear-gradient(135deg, #f8f9fb 0%, #f4f6fa 100%);
        }

        h1 {
            color: white;
            font-weight: 700;
        }

        /* Chart container styling */
        .card-body {
            padding: 25px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 1.5rem;
            }

            .dashboard-subtitle {
                font-size: 0.85rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .stat-icon {
                font-size: 2rem;
            }
        }
    </style>

    {!! $salesChart->script() !!}
    {!! $customerChart->script() !!}
    {!! $itemChart->script() !!}
@endsection
