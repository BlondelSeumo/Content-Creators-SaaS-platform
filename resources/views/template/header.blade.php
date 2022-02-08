<nav class="navbar navbar-expand-md {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white') : (Cookie::get('app_theme') == 'dark' ? 'navbar-dark bg-dark' : 'navbar-light bg-white'))}} shadow-sm ">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}" class="d-inline-block align-top mr-1 ml-3" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <ul class="navbar-nav ml-auto">
                    @if(Auth::check())
                        <li class="nav-item">
                            <a class="nav-link ml-2" href="{{ route('posts.create') }}">{{ __('Create') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ml-2" href="{{ route('feed') }}">{{ __('Feed') }}</a>
                        </li>
                    @endif
                </ul>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-right" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <img src="{{Auth::user()->avatar}}" class="rounded-circle home-user-avatar">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('feed')}}">
                                {{__('Feed')}}
                            </a>
                            <a class="dropdown-item" href="{{route('my.settings')}}">
                                {{__('Settings')}}
                            </a>
                            <a class="dropdown-item" href="{{route('profile',['username'=>Auth::user()->username])}}">
                                {{__('Profile')}}
                            </a>
                            <a class="dropdown-item" href="{{route('my.settings',['type'=>'subscriptions'])}}">
                                {{__('Subscriptions')}}
                            </a>
                            <a class="dropdown-item" href="{{route('my.settings',['type'=>'payments'])}}">
                                {{__('Payments')}}
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
