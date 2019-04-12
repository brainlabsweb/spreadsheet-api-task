<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <section class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#primary-navigation"
                aria-controls="primary-navigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="primary-navigation">
            <ul class="navbar-nav ml-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('google.sign-in') }}">Sign In</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="user-dropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="user-dropdown">
                            <a class="dropdown-item" href="#"
                               onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">Log out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>

                @endguest
            </ul>
        </div>
    </section>
</nav>
