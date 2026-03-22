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
                    <form action="{{ route('addToCart', $item->item_id) }}" method="GET" class="d-inline-flex align-items-center gap-2">
                        <input
                            type="number"
                            name="quantity"
                            min="1"
                            max="{{ $stock->quantity }}"
                            value="1"
                            class="form-control"
                            style="width: 110px;"
                            required
                        >
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button type="submit" formaction="{{ route('buyNow', $item->item_id) }}" class="btn btn-success btn-lg">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </form>
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

        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm review-card">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h4 class="mb-1"><i class="fas fa-star text-warning"></i> Customer Reviews</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-5">{{ $averageRating > 0 ? number_format($averageRating, 1) : '0.0' }}</span>
                                    <span>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($averageRating) ? 'fas' : 'far' }} fa-star text-warning"></i>
                                        @endfor
                                    </span>
                                    <span class="text-muted">({{ $reviews->count() }} review{{ $reviews->count() === 1 ? '' : 's' }})</span>
                                </div>
                            </div>
                        </div>

                        @auth
                            @if($canReview)
                                <form action="{{ route('items.reviews.store', $item->item_id) }}" method="POST" class="review-form mb-4">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Your Rating</label>
                                            <div class="rating-stars" data-initial-rating="{{ old('rating', optional($userReview)->rating) }}">
                                                <input type="hidden" id="rating" name="rating" value="{{ old('rating', optional($userReview)->rating) }}" required>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button" class="star-btn" data-value="{{ $i }}" aria-label="{{ $i }} star">
                                                        <i class="far fa-star"></i>
                                                    </button>
                                                @endfor
                                            </div>
                                            <small class="text-muted d-block mt-1" id="rating-label">Click stars to rate (1 to 5)</small>
                                            @error('rating')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-9">
                                            <label for="comment" class="form-label fw-semibold">Comment</label>
                                            <textarea id="comment" name="comment" rows="3" class="form-control @error('comment') is-invalid @enderror" placeholder="Share your experience with this item...">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            {{ $userReview ? 'Update My Review' : 'Submit Review' }}
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-info mb-4">
                                    You can post a review after your item order is delivered.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mb-4">
                                Please <a href="{{ route('login') }}">log in</a> to review this item.
                            </div>
                        @endauth

                        <div class="review-list">
                            @forelse($reviews as $review)
                                <div class="review-item py-3 border-top">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $review->user->name ?? 'Customer' }}</h6>
                                            <div>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star text-warning small"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ optional($review->created_at)->format('M d, Y h:i A') }}</small>
                                    </div>
                                    @if(!empty($review->comment))
                                        <p class="mb-0 mt-2 text-secondary">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-0">No reviews yet. Be the first to leave feedback.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .review-card {
            border-radius: 14px;
        }

        .review-form {
            background: #f8fbff;
            border: 1px solid #dce9f6;
            border-radius: 12px;
            padding: 16px;
        }

        .review-item:first-child {
            border-top: none !important;
        }

        .rating-stars {
            display: inline-flex;
            gap: 4px;
            align-items: center;
            padding: 6px 8px;
            border: 1px solid #dce9f6;
            border-radius: 10px;
            background: #fff;
        }

        .star-btn {
            border: none;
            background: transparent;
            color: #c0c7d1;
            font-size: 1.25rem;
            line-height: 1;
            padding: 2px;
            cursor: pointer;
        }

        .star-btn.active {
            color: #f3b402;
        }

        .star-btn:hover {
            color: #f3b402;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mainPhoto = document.getElementById('main-item-photo');
                if (mainPhoto) {
                    document.querySelectorAll('.thumbnail-photo').forEach(function (button) {
                        button.addEventListener('click', function () {
                            const nextUrl = button.getAttribute('data-photo-url');
                            if (nextUrl) {
                                mainPhoto.setAttribute('src', nextUrl);
                            }
                        });
                    });
                }

                const ratingWrap = document.querySelector('.rating-stars');
                if (ratingWrap) {
                    const stars = ratingWrap.querySelectorAll('.star-btn');
                    const ratingInput = document.getElementById('rating');
                    const ratingLabel = document.getElementById('rating-label');

                    const paintStars = function (value) {
                        stars.forEach(function (star) {
                            const starValue = parseInt(star.getAttribute('data-value'), 10);
                            star.classList.toggle('active', starValue <= value);
                            star.innerHTML = starValue <= value
                                ? '<i class="fas fa-star"></i>'
                                : '<i class="far fa-star"></i>';
                        });

                        if (value > 0 && ratingLabel) {
                            ratingLabel.textContent = value + ' star' + (value > 1 ? 's' : '') + ' selected';
                        }
                    };

                    const initialRating = parseInt(ratingInput.value || ratingWrap.getAttribute('data-initial-rating') || '0', 10);
                    if (!Number.isNaN(initialRating) && initialRating > 0) {
                        paintStars(initialRating);
                    }

                    stars.forEach(function (star) {
                        star.addEventListener('click', function () {
                            const selected = parseInt(star.getAttribute('data-value'), 10);
                            ratingInput.value = selected;
                            paintStars(selected);
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection
