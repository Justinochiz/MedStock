@extends('layouts.base')

@section('body')
    <div style="min-height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); position: relative; overflow: hidden;">
        <!-- Medical Pattern Background -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05; background-image: url('data:image/svg+xml,<svg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"none\" fill-rule=\"evenodd\"><g fill=\"%23000\" fill-opacity=\"0.5\"><path d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/></g></g></svg>'); background-size: 60px 60px;"></div>

        <!-- Medical Icons Decoration -->
        <div style="position: absolute; top: 20px; right: 20px; font-size: 80px; opacity: 0.1; color: #00a86b;">
            <i class="fas fa-flask"></i>
        </div>
        <div style="position: absolute; bottom: 50px; left: 20px; font-size: 60px; opacity: 0.1; color: #00a86b;">
            <i class="fas fa-capsules"></i>
        </div>

        <div class="container py-5 position-relative" style="z-index: 1;">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg" style="border-top: 4px solid #00a86b;">
                        <div class="card-body p-5">
                            <!-- Medical Icon Header -->
                            <div class="text-center mb-4">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #00a86b 0%, #009966 100%); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h2 class="mb-2" style="font-family: serif; color: #333;">Create New Password</h2>
                                <p class="text-muted">Secure your MedStock account with a new password</p>
                            </div>

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="mb-4">
                                    <label for="email" class="form-label text-uppercase small fw-bold" style="color: #333;">
                                        <i class="fas fa-envelope" style="color: #00a86b; margin-right: 8px;"></i>Email Address
                                    </label>
                                    <input id="email" type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        name="email" value="{{ $request->email ?? old('email') }}"
                                        placeholder="Enter your email" required autocomplete="email" autofocus
                                        style="border: 1px solid #ddd; border-radius: 8px;">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label text-uppercase small fw-bold" style="color: #333;">
                                        <i class="fas fa-lock" style="color: #00a86b; margin-right: 8px;"></i>New Password
                                    </label>
                                    <div class="position-relative">
                                        <input id="password" type="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            name="password" placeholder="Enter new password"
                                            required autocomplete="new-password"
                                            style="border: 1px solid #ddd; border-radius: 8px;">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                            onclick="togglePassword('password')" style="text-decoration: none; color: #00a86b;">
                                            <i class="far fa-eye" id="togglePassword"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password-confirm" class="form-label text-uppercase small fw-bold" style="color: #333;">
                                        <i class="fas fa-check" style="color: #00a86b; margin-right: 8px;"></i>Confirm Password
                                    </label>
                                    <div class="position-relative">
                                        <input id="password-confirm" type="password"
                                            class="form-control form-control-lg"
                                            name="password_confirmation" placeholder="Confirm new password"
                                            required autocomplete="new-password"
                                            style="border: 1px solid #ddd; border-radius: 8px;">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                            onclick="togglePassword('password-confirm')" style="text-decoration: none; color: #00a86b;">
                                            <i class="far fa-eye" id="togglePasswordConfirm"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-lg w-100 mb-4" style="background: linear-gradient(135deg, #00a86b 0%, #009966 100%); border: none; color: white; font-weight: bold;">
                                    <i class="fas fa-check-circle me-2"></i>RESET PASSWORD
                                </button>

                                <div class="text-center">
                                    <p class="text-muted">
                                        Remember your password?
                                        <a href="{{ route('login') }}" class="fw-bold" style="color: #00a86b; text-decoration: none;">
                                            Sign in here
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

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const iconId = fieldId.charAt(0).toUpperCase() + fieldId.slice(1);
            const icon = document.getElementById(`toggle${iconId}`);
            
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

