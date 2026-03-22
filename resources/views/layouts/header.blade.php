<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    {{-- <a class="navbar-brand" href="{{ route('getItems') }}">MedStock</a> --}}
    <a class="navbar-brand" href="{{ route('getItems') }}">
        <img src="{{ asset('images/medstock-logo.svg') }}" alt="MedStock" height="60" class="d-inline-block align-text-top" style="margin-right: 10px;">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                        class="fas fa-user-circle"></i>
                    {{ Auth::check() ? Auth::user()->name : '' }}
                </a>

                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    @if (Auth::check() && Auth::user()->role === 'admin')
                        {{-- <a class="dropdown-item" href="{{ route('admin.orders') }}">Orders</a>
                        <a class="dropdown-item" href="{{ route('admin.users') }}">Users</a> --}}
                        <a class="dropdown-item" href="#">Orders </a>
                        <a class="dropdown-item" href="{{ route('getCart') }}">View Product Cart</a>
                        <a class="dropdown-item" href="{{ route('shop.services') }}">Browse Services</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">User Profile</a>
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit(); return false;">Logout</a>
                    @elseif (Auth::check())
                        <a class="dropdown-item" href="{{ route('getCart') }}">View Product Cart</a>
                        <a class="dropdown-item" href="{{ route('shop.services') }}">Browse Services</a>
                        <a class="dropdown-item" href="{{ route('home') }}#your-orders">Your Orders</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">User Profile</a>
                        {{-- <a class="dropdown-item" href="#">User Profile</a> --}}
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit(); return false;">Logout</a>
                        {{-- <a class="dropdown-item" href="">Logout </a> --}}
                    @else
                        <a class="dropdown-item" href="{{ route('register') }}">register</a>
                        <a class="dropdown-item" href="{{ route('login') }}">Login </a>
                        {{-- <a class="dropdown-item" href="">register</a>
                        <a class="dropdown-item" href="">Login </a> --}}
                    @endif
                </div>
    </div>
    </li>
    @php
        $productCartQty = Session::has('cart') ? (int) Session::get('cart')->totalQty : 0;
    @endphp
    <li class="nav-link">
        <a href="{{ route('getCart') }}" style="font-size: 1.1rem; font-weight: 600; color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-cart-shopping" style="font-size: 1.3rem;"></i> Cart
            <span class="badge rounded-pill bg-danger" style="font-size: 0.85rem; padding: 4px 8px;">{{ $productCartQty > 0 ? $productCartQty : '' }}</span>
        </a>
    </li>
    </ul>
    <form action="{{ route('search') }}" class="form-inline my-2 my-lg-0 ms-4" method="GET" style="display: flex; gap: 8px; align-items: center;">
        {{-- <form action="#" "form-inline my-2 my-lg-0" method="POST"> --}}
        @csrf
        <input class="form-control" type="search" placeholder="Search products & services..." aria-label="Search" name="term" style="min-width: 280px; padding: 10px 15px; font-size: 0.95rem; border: 2px solid white; border-radius: 8px;">
        <button class="btn btn-success my-2 my-sm-0" type="submit" style="padding: 10px 24px; font-weight: 600; font-size: 0.95rem; border-radius: 8px;">Search</button>
    </form>
    </div>
</nav>
