@extends('layouts.base')

@section('body')
    <div style="min-height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); position: relative; overflow: hidden;">
        <!-- Medical Pattern Background -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05; background-image: url('data:image/svg+xml,<svg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"none\" fill-rule=\"evenodd\"><g fill=\"%23000\" fill-opacity=\"0.5\"><path d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/></g></g></svg>'); background-size: 60px 60px;"></div>

        <!-- Medical Icons Decoration -->
        <div style="position: absolute; top: 20px; right: 20px; font-size: 80px; opacity: 0.1; color: #00a86b;">
            <i class="fas fa-heartbeat"></i>
        </div>
        <div style="position: absolute; bottom: 50px; left: 20px; font-size: 60px; opacity: 0.1; color: #00a86b;">
            <i class="fas fa-clipboard-check"></i>
        </div>

        <div class="container py-5 position-relative" style="z-index: 1;">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg" style="border-top: 4px solid #00a86b;">
                        <div class="card-body p-5">
                            <!-- Medical Icon Header -->
                            <div class="text-center mb-4">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #00a86b 0%, #009966 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;">
                                    <i class="fas fa-envelope-open"></i>
                                </div>
                                <h2 class="mb-2" style="font-family: serif; color: #333;">Verify Email Address</h2>
                                <p class="text-muted">Confirm your email to access MedStock</p>
                            </div>

                            @if (session('resent'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d4edda; border-color: #00a86b; color: #155724;">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Success!</strong> A fresh verification link has been sent to your email address.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #00a86b;">
                                <p class="mb-3" style="color: #333;">
                                    <i class="fas fa-info-circle" style="color: #00a86b; margin-right: 10px;"></i>
                                    <strong>Before proceeding</strong>, please check your email for a verification link.
                                </p>
                                <p class="mb-0" style="color: #666;">
                                    If you did not receive the email, you can 
                                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0" style="color: #00a86b; text-decoration: none; font-weight: bold;">request another verification link</button>
                                    </form>.
                                </p>
                            </div>

                            <div class="text-center pt-3">
                                <p class="text-muted mb-0">
                                    <small>Need help? <a href="{{ route('footer.contact') }}" style="color: #00a86b; text-decoration: none;">Contact us</a></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

