@extends('layouts.base')

@section('body')
    <div class="container py-5">
        <div class="footer-info-card mx-auto">
            <h1 class="footer-info-title mb-2">{{ $pageTitle }}</h1>
            <p class="footer-info-subtitle mb-4">{{ $subtitle }}</p>

            <ul class="footer-info-list mb-0">
                @foreach ($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <style>
        .footer-info-card {
            max-width: 860px;
            background: #ffffff;
            border: 1px solid #e3ecf6;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            padding: 1.6rem 1.7rem;
        }

        .footer-info-title {
            color: #0f3765;
            font-size: 1.9rem;
            font-weight: 700;
        }

        .footer-info-subtitle {
            color: #4f6f91;
            font-size: 1rem;
        }

        .footer-info-list {
            color: #1f3e60;
            line-height: 1.75;
            padding-left: 1.2rem;
        }

        .footer-info-list li {
            margin-bottom: 0.5rem;
        }
    </style>
@endsection
