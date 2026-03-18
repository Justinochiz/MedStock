<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" style="border-bottom: 3px solid #4CAF50 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center w-100">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('dashboard.index') }}">
                <img src="{{ asset('images/medstock-logo.svg') }}" alt="MedStock" height="60" class="d-inline-block align-text-top" style="margin-right: 15px;">
            </a>

            <!-- Center Navigation Links (Hidden on Mobile) -->
            <div class="d-none d-lg-flex gap-4 flex-grow-1 justify-content-center">
                <a href="{{ route('dashboard.index') }}" 
                   class="nav-link-custom @if(Route::currentRouteName() === 'dashboard.index') active @endif"
                   style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">
                    DASHBOARD
                </a>
                <a href="{{ route('items.index') }}" 
                   class="nav-link-custom @if(Route::currentRouteName() === 'items.index') active @endif"
                   style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">
                    INVENTORY
                </a>
                <a href="{{ route('admin.orders') }}" 
                   class="nav-link-custom @if(Route::currentRouteName() === 'admin.orders') active @endif"
                   style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">
                    ORDERS
                </a>
                <a href="{{ route('admin.users') }}" 
                   class="nav-link-custom @if(Route::currentRouteName() === 'admin.users') active @endif"
                   style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">
                    USERS
                </a>
                <a href="{{ route('admin.discount-codes') }}" 
                   class="nav-link-custom @if(Route::currentRouteName() === 'admin.discount-codes') active @endif"
                   style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px;">
                    DISCOUNTS
                </a>
            </div>

            <!-- Right Side: Mobile Toggle and User Menu -->
            <div class="d-flex align-items-center gap-3">
                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle p-2 text-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: 2px solid #f0f0f0;">
                        <i class="fas fa-user text-muted" style="font-size: 1.1rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                        <li><span class="dropdown-header">{{ Auth::user()->name }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-admin').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form-admin" action="{{ route('user.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .nav-link-custom {
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        padding-bottom: 8px;
    }

    .nav-link-custom:hover {
        color: #4CAF50;
    }

    .nav-link-custom.active {
        color: #4CAF50;
        font-weight: 600;
    }

    .nav-link-custom.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #4CAF50;
        border-radius: 2px;
    }

    #userDropdown:hover {
        background-color: #f8f9fa !important;
        border-color: #4CAF50 !important;
    }

    .dropdown-menu {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #4CAF50;
    }
</style>
