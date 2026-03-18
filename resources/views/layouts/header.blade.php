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
            <li class="nav-item active">
                {{-- <a class="nav-link" href="{{ route('getItems') }}">Home<span class="sr-only">(current)</span></a> --}}
                @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                    <a class="nav-link" href="{{ Auth::user()->role === 'admin' ? route('dashboard.index') : route('getItems') }}">Home<span class="sr-only"></span></a>
                @else
                    <a class="nav-link" href="{{ route('getItems') }}">Home<span class="sr-only"></span></a>
                @endif

            </li>

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
                        <a class="dropdown-item" href="{{ route('services.cart') }}">View Service Cart</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">User Profile</a>
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit(); return false;">Logout</a>
                    @elseif (Auth::check())
                        <a class="dropdown-item" href="{{ route('getCart') }}">View Product Cart</a>
                        <a class="dropdown-item" href="{{ route('services.cart') }}">View Service Cart</a>
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
        $serviceCartRaw = Session::get('service_cart', []);
        $serviceCartQty = 0;

        if (is_array($serviceCartRaw)) {
            foreach ($serviceCartRaw as $serviceLine) {
                $serviceCartQty += (int) ($serviceLine['qty'] ?? 0);
            }
        }

        $combinedCartQty = $productCartQty + $serviceCartQty;
    @endphp
    <li class="nav-link">
        <a href="{{ route('services.cart') }}">
            {{-- <a href=""> --}}
            <i class="fa-solid fa-cart-shopping"></i> Service Cart
            <span
                class="badge rounded-pill bg-danger">{{ $combinedCartQty > 0 ? $combinedCartQty : '' }}</span>
        </a>

    </li>
    </ul>
    <form action="{{ route('search') }}" class="form-inline my-2 my-lg-0 ms-4" method="GET">
        {{-- <form action="#" "form-inline my-2 my-lg-0" method="POST"> --}}
        @csrf
        <select class="form-select me-2" name="type" style="max-width: 140px;">
            <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All</option>
            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Products</option>
            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Services</option>
        </select>
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="term">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    </div>
</nav>
