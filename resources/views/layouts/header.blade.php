<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    {{-- <a class="navbar-brand" href="{{ route('getItems') }}">MedStock</a> --}}
    <a class="navbar-brand" href="/">
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
                <a class="nav-link" href="">Home<span class="sr-only"></span></a>

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
                        <a class="dropdown-item" href="#">User Profile</a>
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit(); return false;">Logout</a>
                    @elseif (Auth::check())
                        <a class="dropdown-item" href="{{ route('customers.create') }}">User Profile</a>
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
    <li class="nav-link">
        <a href="{{ route('getCart') }}">
            {{-- <a href=""> --}}
            <i class="fa-solid fa-cart-shopping"></i> Shopping Cart
            <span
                class="badge rounded-pill bg-danger">{{ Session::has('cart') ? Session::get('cart')->totalQty : '' }}</span>
        </a>

    </li>
    </ul>
    <form action="{{ route('search') }}" class="form-inline my-2 my-lg-0 ms-4" method="GET">
        {{-- <form action="#" "form-inline my-2 my-lg-0" method="POST"> --}}
        @csrf
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="term">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    </div>
</nav>
