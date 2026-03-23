@extends('layouts.base')

@section('title')
    Discount Codes
@endsection

@section('body')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Discount Codes</h2>
                <p class="text-muted mb-0"> promo codes available for customer.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm discount-card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discountCodes as $discount)
                                <tr>
                                    <td><span class="promo-code-badge">{{ $discount['code'] }}</span></td>
                                    <td><strong class="text-success">{{ $discount['percent'] }}% OFF</strong></td>
                                    <td class="text-muted">Apply during checkout to reduce the product total.</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .discount-card {
            border-radius: 14px;
        }

        .promo-code-badge {
            display: inline-block;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            background: #f4f7fb;
            border: 1px solid #dbe4f0;
            font-weight: 700;
            letter-spacing: 0.04em;
        }
    </style>
@endsection
