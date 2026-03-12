<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Brand Section -->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="mb-3 fw-bold">MedStock</h4>
                <p class="text-muted small" style="line-height: 1.6;">Track what you buy. Track what you use. Manage your medical inventory.</p>
            </div>

            <!-- About Us Section -->
            <div class="col-lg-2 col-md-4 mb-5 mb-lg-0">
                <h6 class="text-uppercase mb-4 fw-bold letter-spacing" style="font-size: 0.85rem;">About Us</h6>
                <ul class="list-unstyled">
                    <li class="mb-3"><a href="{{ route('getItems') }}" class="text-muted text-decoration-none small" style="transition: color 0.3s;">About</a></li>
                    <li class="mb-3"><a href="{{ route('getItems') }}" class="text-muted text-decoration-none small" style="transition: color 0.3s;">Features</a></li>
                    <li class="mb-3"><a href="/" class="text-muted text-decoration-none small" style="transition: color 0.3s;">Contact</a></li>
                </ul>
            </div>

            <!-- Account Section -->
            <div class="col-lg-2 col-md-4 mb-5 mb-lg-0">
                <h6 class="text-uppercase mb-4 fw-bold letter-spacing" style="font-size: 0.85rem;">Account</h6>
                <ul class="list-unstyled">
                    @guest
                        <li class="mb-3"><a href="{{ route('login') }}" class="text-muted text-decoration-none small" style="transition: color 0.3s;">Login/Register</a></li>
                    @else
                        <li class="mb-3"><a href="#" class="text-muted text-decoration-none small" style="transition: color 0.3s;" 
                            onclick="event.preventDefault(); document.getElementById('logout-form-footer').submit();">Logout</a></li>
                        <form id="logout-form-footer" action="{{ route('user.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>

            <!-- Help Section -->
            <div class="col-lg-2 col-md-4 mb-5 mb-lg-0">
                <h6 class="text-uppercase mb-4 fw-bold letter-spacing" style="font-size: 0.85rem;">Help</h6>
                <ul class="list-unstyled">
                    <li class="mb-3"><a href="{{ route('getCart') }}" class="text-muted text-decoration-none small" style="transition: color 0.3s;">Cart</a></li>
                    <li class="mb-3"><a href="/" class="text-muted text-decoration-none small" style="transition: color 0.3s;">Support</a></li>
                </ul>
            </div>
        </div>

        <!-- Copyright Section -->
        <hr class="border-secondary my-5" style="opacity: 0.3;">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted mb-0 small">© 2026 MedStock. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="/" class="text-muted text-decoration-none me-4 small" style="transition: color 0.3s;">Privacy Policy</a>
                <a href="/" class="text-muted text-decoration-none small" style="transition: color 0.3s;">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<style>
    footer a {
        cursor: pointer;
    }
    
    footer a:hover {
        color: #fff !important;
    }
    
    .letter-spacing {
        letter-spacing: 0.5px;
    }
</style>

