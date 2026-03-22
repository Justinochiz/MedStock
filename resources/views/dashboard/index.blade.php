@extends('layouts.base')
@section('title')
    Admin Dashboard - MedStock
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

        <!-- Analytics Charts Section -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card medical-card border-0">
                    <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i> Sales Trends
                        </h5>
                        <form method="GET" action="{{ url()->current() }}" class="sales-filter-form d-flex align-items-center flex-wrap gap-2">
                            <label for="date-from" class="small text-muted mb-0">From</label>
                            <input type="date" id="date-from" name="date_from" class="form-control form-control-sm" value="{{ $dateFromValue }}">

                            <label for="date-to" class="small text-muted mb-0">To</label>
                            <input type="date" id="date-to" name="date_to" class="form-control form-control-sm" value="{{ $dateToValue }}">

                            <button type="submit" class="btn btn-sm btn-primary sales-filter-btn">Apply</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <p class="sales-chart-caption mb-3">
                            Track yearly gross sales by date range.
                            @if($dateFromValue || $dateToValue)
                                Date range:
                                <strong>{{ $dateFromValue ?: 'Any start' }}</strong>
                                to
                                <strong>{{ $dateToValue ?: 'Any end' }}</strong>
                            @endif
                        </p>
                        <div id="sales-chart-yearly" class="sales-chart-view">
                            {!! $yearlySalesChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card medical-card border-0">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-injured"></i> User Analytics & Demographics
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
                            <i class="fas fa-vial"></i> Product Sales Contribution (%)
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
            background:
                radial-gradient(circle at 15% 10%, rgba(0, 204, 255, 0.12), transparent 28%),
                radial-gradient(circle at 88% 18%, rgba(0, 153, 255, 0.1), transparent 24%),
                linear-gradient(145deg, #f8fdff 0%, #f3f9ff 45%, #eef6ff 100%);
            min-height: 100vh;
            padding: 30px 15px;
            position: relative;
            overflow: hidden;
        }

        .medical-dashboard::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(84, 205, 255, 0.12) 1px, transparent 1px), linear-gradient(90deg, rgba(84, 205, 255, 0.12) 1px, transparent 1px);
            background-size: 34px 34px;
            pointer-events: none;
            z-index: 0;
        }

        .medical-dashboard > .row {
            position: relative;
            z-index: 1;
        }

        .dashboard-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.97) 0%, rgba(244, 251, 255, 0.98) 100%);
            color: #14477a;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(25, 94, 145, 0.12);
            border: 1px solid rgba(93, 208, 255, 0.35);
            border-left: 4px solid #0ac5e5;
        }

        .dashboard-title {
            color: #0e4c7f;
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 0 14px rgba(86, 223, 255, 0.18);
        }

        .dashboard-subtitle {
            color: #3d6286;
            margin: 10px 0 0 32px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .medical-card {
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid rgba(96, 187, 235, 0.28);
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.96) 0%, rgba(244, 250, 255, 0.97) 100%);
            box-shadow: 0 8px 24px rgba(16, 95, 145, 0.1);
            backdrop-filter: blur(4px);
        }

        .medical-card:hover {
            box-shadow: 0 14px 30px rgba(12, 160, 220, 0.18);
            transform: translateY(-2px);
            border-color: rgba(121, 224, 255, 0.58);
        }

        .stat-label {
            color: #4d7297;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            color: #0a6ec7;
            font-weight: 700;
            font-size: 2rem;
            margin: 0;
            text-shadow: 0 0 10px rgba(111, 216, 255, 0.14);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.92;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 0 8px rgba(120, 220, 255, 0.2));
        }

        .medical-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .card-title {
            color: #11538b;
            font-weight: 700;
            font-size: 1.1rem;
            text-shadow: 0 0 8px rgba(118, 219, 255, 0.15);
        }

        .card-header {
            border-bottom: 1px solid rgba(90, 175, 230, 0.22);
            background: linear-gradient(135deg, rgba(243, 250, 255, 0.85) 0%, rgba(237, 247, 255, 0.9) 100%);
        }

        .sales-chart-view {
            min-height: 360px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 252, 255, 0.98) 100%);
            border: 1px solid rgba(93, 195, 243, 0.32);
            border-radius: 12px;
            padding: 12px;
            box-shadow: inset 0 0 0 1px rgba(133, 228, 255, 0.16), 0 10px 20px rgba(15, 96, 148, 0.12);
        }

        .sales-chart-view canvas {
            min-height: 332px;
        }

        .sales-chart-view.is-hidden {
            display: none;
        }

        .sales-chart-caption {
            color: #527a9f;
            font-weight: 500;
            font-size: 0.92rem;
        }

        .sales-filter-form .form-control,
        .sales-filter-form .form-select {
            border: 1px solid rgba(102, 202, 255, 0.54);
            color: #165a8f;
            background: #ffffff;
            font-weight: 600;
            min-width: 140px;
        }

        .sales-filter-form .form-control:focus,
        .sales-filter-form .form-select:focus {
            border-color: #54c3ef;
            box-shadow: 0 0 0 0.18rem rgba(84, 210, 255, 0.16);
        }

        .sales-filter-btn {
            background: linear-gradient(135deg, #1f8ed8 0%, #1069b3 100%);
            border: none;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
        }

        .sales-filter-btn:hover,
        .sales-filter-btn:focus {
            background: linear-gradient(135deg, #2b9ae2 0%, #0f78c9 100%);
            box-shadow: 0 6px 14px rgba(22, 113, 184, 0.22);
        }

        .small.text-muted {
            color: #4f7ca5 !important;
            font-weight: 600;
        }

        h1 {
            color: #0e4c7f;
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

            .sales-filter-form {
                width: 100%;
            }

            .sales-filter-form .form-control,
            .sales-filter-form .form-select,
            .sales-filter-btn {
                width: 100%;
            }
        }

        @keyframes pulseGlow {
            0% {
                box-shadow: 0 0 0 0 rgba(85, 214, 255, 0.22);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(85, 214, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(85, 214, 255, 0);
            }
        }

        .sales-chart-view:not(.is-hidden) {
            animation: pulseGlow 3.4s ease-out infinite;
        }
    </style>

    {!! $yearlySalesChart->script() !!}
    {!! $customerChart->script() !!}
    {!! $itemChart->script() !!}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const setNeonColors = function (chart) {
                if (!chart || !chart.data || !chart.data.datasets) {
                    return;
                }

                chart.data.datasets.forEach(function (dataset) {
                    if (!dataset) {
                        return;
                    }

                    if (chart.config && chart.config.type === 'bar' && typeof dataset.backgroundColor === 'string') {
                        dataset.backgroundColor = dataset.backgroundColor.replace('0.45', '0.72').replace('0.60', '0.72').replace('0.62', '0.72').replace('0.65', '0.72');
                    }

                    if (chart.config && chart.config.type === 'bar' && !dataset.borderColor) {
                        dataset.borderColor = 'rgba(120, 235, 255, 0.8)';
                    }

                    if (chart.config && chart.config.type === 'bar') {
                        dataset.borderWidth = 1;
                    }

                    if (chart.config && chart.config.type === 'doughnut') {
                        dataset.borderWidth = 2;
                        dataset.borderColor = '#ffffff';
                    }
                });
            };

            const applyFuturisticChartTheme = function () {
                if (!window.Chart || !window.Chart.instances) {
                    return;
                }

                Object.keys(window.Chart.instances).forEach(function (instanceKey) {
                    const chart = window.Chart.instances[instanceKey];
                    if (!chart || !chart.options) {
                        return;
                    }

                    chart.options.legend = chart.options.legend || {};
                    chart.options.legend.labels = chart.options.legend.labels || {};
                    chart.options.legend.labels.fontColor = '#2b628f';
                    chart.options.legend.labels.fontStyle = '600';

                    chart.options.tooltips = chart.options.tooltips || {};
                    chart.options.tooltips.backgroundColor = 'rgba(255, 255, 255, 0.98)';
                    chart.options.tooltips.titleFontColor = '#0f4a7c';
                    chart.options.tooltips.bodyFontColor = '#285f8d';
                    chart.options.tooltips.borderColor = 'rgba(84, 193, 235, 0.65)';
                    chart.options.tooltips.borderWidth = 1;

                    if (chart.options.scales) {
                        (chart.options.scales.xAxes || []).forEach(function (xAxis) {
                            xAxis.ticks = xAxis.ticks || {};
                            xAxis.ticks.fontColor = '#4a7195';

                            xAxis.gridLines = xAxis.gridLines || {};
                            xAxis.gridLines.color = 'rgba(87, 175, 220, 0.18)';
                            xAxis.gridLines.zeroLineColor = 'rgba(114, 219, 255, 0.24)';
                        });

                        (chart.options.scales.yAxes || []).forEach(function (yAxis) {
                            yAxis.ticks = yAxis.ticks || {};
                            yAxis.ticks.fontColor = '#4a7195';

                            yAxis.gridLines = yAxis.gridLines || {};
                            yAxis.gridLines.color = 'rgba(87, 175, 220, 0.2)';
                            yAxis.gridLines.zeroLineColor = 'rgba(114, 219, 255, 0.26)';
                        });
                    }

                    setNeonColors(chart);
                    chart.update();
                });
            };

            setTimeout(function () {
                applyFuturisticChartTheme();
            }, 40);
        });
    </script>
@endsection
