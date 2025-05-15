<nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm" style="z-index: 99999">
    <div class="container">
        <a class="navbar-brand" href="/">WebGis</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" aria-current="page"
                        href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('data/penduduk') ? 'active' : '' }}" href="/data/penduduk">Data
                        penduduk</a>
                </li>
                <li class="nav-item dropdown">
                    @guest
                        <a class="btn btn-success" href="{{ route('login') }}">Login</a>
                    @else
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownMenuLink"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a>
                            </li>
                            @can('Admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('tanah.index') }}">Dashboard</a>
                                </li>
                            @endcan
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    @endguest
                </li>
            </ul>
        </div>
    </div>
</nav>
