@extends('layouts.base')

@section('body')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                @php
                    $rawGallery = $item->imageGallery();
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

                    $primaryPhoto = $gallery[0];
                @endphp

                @if($primaryPhoto)
                    <img id="main-item-photo" src="{{ $primaryPhoto }}" alt="{{ $item->description }}" class="img-fluid rounded shadow" style="width: 100%; max-height: 420px; object-fit: cover;">

                    @if(count($gallery) > 1)
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @foreach($gallery as $photoUrl)
                                <button type="button" class="btn p-0 border-0 thumbnail-photo" data-photo-url="{{ $photoUrl }}">
                                    <img src="{{ $photoUrl }}" alt="{{ $item->description }} photo" width="75" height="75" style="object-fit: cover; border-radius: 4px;">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                        <p class="text-muted">No image available</p>
                    </div>
                @endif
            </div>
            
            <div class="col-md-6">
                <h1 class="mb-3">{{ $item->description }}</h1>
                
                <div class="mb-4">
                    <h3 class="text-primary mb-2">₱{{ number_format($item->sell_price, 2) }}</h3>
                    @if($stock && $stock->quantity > 0)
                        <p class="text-success">
                            <i class="fas fa-check-circle"></i> In Stock ({{ $stock->quantity }} available)
                        </p>
                    @else
                        <p class="text-danger">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </p>
                    @endif
                </div>

                <div class="mb-4">
                    <h5>Item Details</h5>
                    <ul class="list-unstyled">
                        <li><strong>Sell Price:</strong> ₱{{ number_format($item->sell_price, 2) }}</li>
                        @if($stock)
                            <li><strong>Available Quantity:</strong> {{ $stock->quantity }}</li>
                        @endif
                    </ul>
                </div>

                @if($stock && $stock->quantity > 0)
                    <a href="{{ route('addToCart', $item->item_id) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </a>
                @else
                    <button class="btn btn-secondary btn-lg" disabled>
                        Out of Stock
                    </button>
                @endif

                <a href="{{ route('getItems') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Shop
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mainPhoto = document.getElementById('main-item-photo');
                if (!mainPhoto) {
                    return;
                }

                document.querySelectorAll('.thumbnail-photo').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const nextUrl = button.getAttribute('data-photo-url');
                        if (nextUrl) {
                            mainPhoto.setAttribute('src', nextUrl);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
