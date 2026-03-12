<!-- Admin Sidebar Navigation -->
@if(Auth::check() && Auth::user()->role === 'admin')
<nav class="sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <h3 class="navbar-brand mb-0">
            <i class="fas fa-hospital"></i> iMedStock
        </h3>
        <button type="button" class="btn-close sidebar-toggle d-lg-none" aria-label="Close Sidebar"></button>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'dashboard.index') active @endif" 
               href="{{ route('dashboard.index') }}">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'items.index') active @endif" 
               href="{{ route('items.index') }}">
                <i class="fas fa-boxes"></i>
                <span>Equipment</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.orders') active @endif" 
               href="{{ route('admin.orders') }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.customers') active @endif" 
               href="{{ route('admin.customers') }}">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName() === 'admin.users') active @endif" 
               href="{{ route('admin.users') }}">
                <i class="fas fa-user-cog"></i>
                <span>Users</span>
            </a>
        </li>

        <li class="nav-item mt-4 pt-3 border-top">
            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a class="nav-link text-danger" href="#" onclick="document.getElementById('logout-form').submit(); return false;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

<!-- Sidebar Toggle Button (Mobile) -->
<button type="button" class="btn btn-primary sidebar-toggle-btn d-lg-none position-fixed" 
        style="bottom: 20px; right: 20px; z-index: 999; border-radius: 50%; width: 50px; height: 50px; padding: 0;">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Styles -->
<style>
    .sidebar {
        width: 250px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        min-height: 100vh;
        padding: 20px 0;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        overflow-y: auto;
        transform: translateX(0);
        transition: transform 0.3s ease;
    }

    .sidebar.hidden {
        transform: translateX(-100%);
    }

    .sidebar-header {
        padding: 0 20px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar-header h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
    }

    .sidebar .nav {
        padding: 20px 0;
    }

    .sidebar .nav-item {
        margin: 0;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .sidebar .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
        border-left-color: white;
    }

    .sidebar .nav-link.active {
        color: white;
        background-color: rgba(255, 255, 255, 0.15);
        border-left-color: white;
        font-weight: 600;
    }

    .sidebar .nav-link i {
        width: 24px;
        margin-right: 12px;
        text-align: center;
    }

    .sidebar .nav-link span {
        font-size: 0.95rem;
    }

    /* Main content area adjustment when sidebar is visible */
    body.sidebar-open {
        margin-left: 250px;
    }

    body.sidebar-open .navbar {
        margin-left: 0;
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1050;
        }

        .sidebar.hidden {
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

    @media (max-width: 768px) {
        .sidebar {
            width: 250px;
        }
    }

    .sidebar-toggle-btn {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
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
