@extends('layouts.base')

@section('body')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs border-0 mb-4" role="tablist">
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link active border-0 border-bottom border-3 border-dark pb-3" 
                                   id="signin-tab" data-bs-toggle="tab" href="#signin" role="tab">
                                    <strong>SIGN IN</strong>
                                </a>
                            </li>
                            <li class="nav-item flex-fill text-center">
                                <a class="nav-link border-0 pb-3 text-muted" 
                                   id="register-tab" href="{{ route('register') }}">
                                    <strong>CREATE ACCOUNT</strong>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="signin" role="tabpanel">
                                <!-- Message Alert -->
                                @if(session('message'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>{{ session('message') }}</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="text-center mb-5">
                                    <h2 class="mb-2" style="font-family: serif;">Hello</h2>
                                    <p class="text-muted">Sign in to continue your journey</p>
                                </div>

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="mb-4">
                                        <label for="email" class="form-label text-uppercase small fw-bold">
                                            Email Address
                                        </label>
                                        <input id="email" type="email"
                                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}"
                                            placeholder="Enter your email" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="password" class="form-label text-uppercase small fw-bold">
                                            Password
                                        </label>
                                        <div class="position-relative">
                                            <input id="password" type="password"
                                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                name="password" placeholder="Enter your password"
                                                required autocomplete="current-password">
                                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                                onclick="togglePassword('password')" style="text-decoration: none;">
                                                <i class="far fa-eye" id="togglePassword"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-dark btn-lg w-100 mb-4">
                                        SIGN IN
                                    </button>

                                    <div class="text-center mb-3">
                                        <p class="text-muted">
                                            <a href="{{ route('password.request') }}" class="text-dark text-decoration-none">
                                                Forgot Password?
                                            </a>
                                        </p>
                                    </div>

                                    <div class="text-center">
                                        <p class="text-muted">
                                            Don't have an account? 
                                            <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none">
                                                Sign up here
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
        }
        .nav-tabs .nav-link.active {
            color: #000;
            background: none;
        }
        .form-control:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById('togglePassword');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
