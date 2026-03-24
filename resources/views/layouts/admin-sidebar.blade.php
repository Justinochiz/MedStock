<!-- Admin Sidebar Navigation - Medical Shop Theme -->
@if(Auth::check() && Auth::user()->role === 'admin')
<nav class="sidebar medical-sidebar" id="adminSidebar">
    <div class="sidebar-header medical-header">
        <div class="logo-container">
            <a href="{{ route('dashboard.index') }}" class="navbar-brand d-flex align-items-center gap-2">
                <img src="{{ asset('images/medstock-logo.svg') }}" alt="MedStock" height="60" class="d-inline-block">
            </a>
        </div>
        <button type="button" class="btn-close sidebar-toggle d-lg-none" aria-label="Close Sidebar"></button>
    </div>

    <!-- Admin Profile Card -->
    <div class="admin-profile-card medical-profile">
        @php
            $admin = Auth::user();
            $adminPhotoUrl = (!empty($admin->photo_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($admin->photo_path))
                ? asset('storage/' . $admin->photo_path)
                : null;
        @endphp
        <div class="d-flex align-items-center">
            <div class="profile-avatar medical-avatar">
                @if($adminPhotoUrl)
                    <img src="{{ $adminPhotoUrl }}" alt="{{ $admin->name }}" class="avatar-photo">
                @else
                    <div class="avatar-initials">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                @endif
                <span class="online-indicator"></span>
            </div>
            <div class="profile-info">
                <h6 class="profile-name mb-1">{{ $admin->name }}</h6>
                <p class="profile-role">Admin</p>
                <div class="d-flex align-items-center">
                    <span class="online-dot"></span>
                    <span class="online-text">Online</span>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav flex-column medical-nav">
        <form id="logout-form-admin-sidebar" action="{{ route('user.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <li class="nav-section-label">
            <span>MAIN</span>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'dashboard.index') active @endif" 
               href="{{ route('dashboard.index') }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-section-label mt-3">
            <span>OPERATIONS</span>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'items.index') active @endif" 
               href="{{ route('items.index') }}">
                <i class="fas fa-pills"></i>
                <span>Equipment</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::is('services.*')) active @endif"
               href="{{ route('services.index') }}">
                <i class="fas fa-briefcase-medical"></i>
                <span>Services</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.orders') active @endif" 
               href="{{ route('admin.orders') }}">
                <i class="fas fa-file-prescription"></i>
                <span>Orders</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.reviews') active @endif" 
               href="{{ route('admin.reviews') }}">
                <i class="fas fa-star-half-alt"></i>
                <span>Reviews</span>
            </a>
        </li>

        <li class="nav-section-label mt-3">
            <span>MANAGEMENT</span>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.users') active @endif" 
               href="{{ route('admin.users') }}">
                <i class="fas fa-user-tie"></i>
                <span>Users</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.discount-codes') active @endif" 
               href="{{ route('admin.discount-codes') }}">
                <i class="fas fa-ticket-alt"></i>
                <span>Promotions</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link logout-link" href="#" onclick="document.getElementById('logout-form-admin-sidebar').submit(); return false;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-bottom-banner" aria-hidden="true"></div>

</nav>

<!-- Sidebar Toggle Button (Mobile) -->
<button type="button" class="btn sidebar-toggle-btn d-lg-none position-fixed" 
        style="bottom: 20px; right: 20px; z-index: 999; border-radius: 50%; width: 50px; height: 50px; padding: 0; background: linear-gradient(135deg, #0066CC 0%, #004494 100%); color: white; border: none; box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Styles - Medical Theme -->
<style>
    .medical-sidebar {
        width: 250px;
        background: linear-gradient(180deg, #f8f9fb 0%, #f4f6fa 100%);
        color: #2c3e50;
        min-height: 100vh;
        height: 100vh;
        padding: 0;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        overflow-y: scroll;
        overflow-x: hidden;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        transform: translateX(0);
        transition: transform 0.3s ease;
        border-right: 2px solid #e8ecf4;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
    }

    .medical-sidebar.hidden {
        transform: translateX(-100%);
    }

    /* Medical Header Styling */
    .medical-header {
        padding: 20px;
        background: linear-gradient(135deg, #0066CC 0%, #004494 100%);
        color: white;
        border-bottom: 3px solid #4CAF50;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .medical-logo {
        display: none;
    }

    .medical-header .navbar-brand {
        color: white;
        text-decoration: none;
    }

    .medical-header .navbar-brand img {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .tagline {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 2px 0 0 0;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        font-weight: 500;
    }

    /* Medical Profile Card */
    .medical-profile {
        padding: 16px;
        margin: 16px 10px;
        background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
        border: 1.5px solid #b3d9ff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.08);
    }

    .medical-avatar .avatar-initials {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0066CC 0%, #004494 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: bold;
        color: white;
        border: 2px solid #004494;
    }

    .medical-avatar .avatar-photo {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #004494;
        display: block;
    }

    .profile-avatar {
        position: relative;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .profile-info {
        flex: 1;
        min-width: 0;
    }

    .profile-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0066CC;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-role {
        font-size: 0.75rem;
        color: #666;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-weight: 500;
    }

    .online-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 14px;
        height: 14px;
        background-color: #4CAF50;
        border: 3px solid white;
        border-radius: 50%;
        animation: medical-pulse 2s infinite;
    }

    @keyframes medical-pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
        }
        50% {
            box-shadow: 0 0 0 6px rgba(76, 175, 80, 0);
        }
    }

    .online-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: #4CAF50;
        border-radius: 50%;
        margin-right: 6px;
    }

    .online-text {
        font-size: 0.75rem;
        color: #4CAF50;
        font-weight: 600;
    }

    /* Medical Navigation */
    .medical-nav {
        padding: 12px 0 64px;
    }

    .medical-sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .medical-sidebar::-webkit-scrollbar-thumb {
        background: #8fb4e6;
        border-radius: 10px;
    }

    .medical-sidebar::-webkit-scrollbar-track {
        background: #e9eef5;
    }

    .sidebar-bottom-banner {
        height: 48px;
        margin: 0 12px 12px;
        border-radius: 10px;
        border-top: 3px solid #4CAF50;
        background: linear-gradient(135deg, #0066CC 0%, #004494 100%);
        box-shadow: 0 4px 10px rgba(0, 102, 204, 0.25);
    }

    .nav-section-label {
        display: list-item;
        padding: 12px 20px 8px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-top: 8px;
    }

    .nav-section-label:first-child {
        margin-top: 0;
    }

    .nav-item {
        margin: 0;
    }

    .medical-sidebar .nav-link {
        color: #2c5aa0;
        padding: 11px 20px;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        position: relative;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .medical-sidebar .nav-link i {
        width: 20px;
        margin-right: 12px;
        text-align: center;
        font-size: 1rem;
    }

    .medical-sidebar .nav-link:hover {
        color: #0066CC;
        background-color: #f0f4f9;
        border-left-color: #0066CC;
        padding-left: 18px;
    }

    .medical-sidebar .nav-link.active {
        color: white;
        background: linear-gradient(90deg, #0066CC 0%, #004494 100%);
        border-left-color: #4CAF50;
        padding-left: 18px;
        font-weight: 600;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .logout-link {
        color: #e74c3c !important;
        border-left-color: transparent !important;
        font-weight: 600 !important;
        background: rgba(231, 76, 60, 0.06);
    }

    .logout-link:hover {
        color: #c0392b !important;
        background-color: #fef5f4 !important;
        border-left-color: #e74c3c !important;
    }

    @media (max-height: 820px) {
        .medical-profile {
            margin-bottom: 10px;
            padding: 12px;
        }

        .medical-nav {
            padding-bottom: 8px;
        }

        .sidebar-bottom-banner {
            display: none;
        }
    }

    /* Main content area adjustment */
    body.sidebar-open {
        margin-left: 250px;
    }

    body.sidebar-open .navbar {
        margin-left: 0;
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
        .medical-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1050;
        }

        .medical-sidebar.hidden {
            transform: translateX(-100%);
        }

        body.sidebar-open {
            margin-left: 0;
        }

        body.sidebar-open::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-toggle:hover {
        opacity: 0.8;
    }
</style>

<!-- Sidebar Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtns = document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-btn');

        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('hidden');
                document.body.classList.toggle('sidebar-open');
            });
        });

        // Close sidebar on link click (mobile)
        if (window.innerWidth < 992) {
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    sidebar.classList.add('hidden');
                    document.body.classList.remove('sidebar-open');
                });
            });
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('hidden');
            }
        });
    });
</script>
@endif
