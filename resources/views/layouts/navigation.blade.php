<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">ITD Portfolio</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left side -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/') }}">Home</a>
                </li>
                <li class="nav-item {{ request()->routeIs('posts.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('posts.index') }}">Posts</a>
                </li>
            </ul>

            <!-- Right side -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item {{ request()->routeIs('profile.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('profile.index') }}">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->name }}
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-warning text-dark ms-1">Moderator</span>
                            @else
                                <span class="badge text-white ms-1" style="background-color: #000dfdff; color: #222;">Beginner</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-link btn btn-link" type="submit">Log out</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('register') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('register') }}">Sign up</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>