@extends('layouts.base')

@section('title')
    Medical Services
@endsection

@section('body')
    @include('layouts.flash-messages')

    <div class="container-fluid py-5 shop-hero">
        <div class="container">
            <p class="text-uppercase small fw-semibold text-light-emphasis mb-2">Professional Care</p>
            <h1 class="text-white mb-2"><i class="fas fa-briefcase-medical me-2"></i>Medical Services</h1>
            <p class="text-white-50 mb-0">Choose trusted services and book them instantly.</p>
        </div>
    </div>

    <div class="container my-5">
        @if($services->count() > 0)
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Available Services</h4>
                <span class="text-muted small">{{ $services->count() }} service(s) available</span>
            </div>

            <div class="row">
                @foreach ($services as $service)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 service-card">
                            @php
                                $rawGallery = $service->imageGallery();
                                $gallery = [];
                                foreach ($rawGallery as $photo) {
                                    $storagePath = str_replace('public/', '', (string) $photo);
                                    if (!empty($storagePath) && Storage::disk('public')->exists($storagePath)) {
                                        $gallery[] = asset('storage/' . $storagePath);
                                    }
                                }
                                if (empty($gallery)) {
                                    $gallery[] = asset('images/medstock-logo.png');
                                }
                            @endphp
                            <img src="{{ $gallery[0] }}" alt="{{ $service->name }}" class="card-img-top" style="height: 240px; object-fit: cover;">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $service->name }}</h5>
                                <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit((string) $service->description, 95) }}</p>
                                <h4 class="text-primary mb-3">P{{ number_format($service->price, 2) }}</h4>

                                <div class="mt-auto d-grid gap-2">
                                    <a href="{{ route('shop.services.show', $service->service_id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-briefcase-medical" style="font-size: 4rem; color: #c9ced6;"></i>
                <h3 class="mt-3 text-muted">No Services Available</h3>
            </div>
        @endif
    </div>

    <style>
        .shop-hero {
            background: radial-gradient(circle at top right, #33b7a0 0%, #104e8b 48%, #0c1f36 100%);
            min-height: 220px;
        }

        .service-card {
            border-radius: 12px;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .service-card:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12) !important;
            transform: translateY(-4px);
        }
    </style>
@endsection
