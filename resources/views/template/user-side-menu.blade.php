<nav class="sidebar {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'light') : (Cookie::get('app_theme') == 'dark' ? '' : 'light'))}}">

    <!-- close sidebar menu -->
    <div class="col-12 pb-1">
        <div class="dismiss d-flex justify-content-center align-items-center flex-row">
            @include('elements.icon',['icon'=>'arrow-back','variant'=>'medium'])
        </div>
    </div>

    <div class="col-12 sidebar-wrapper">

        <div class="mb-4 d-flex flex-row-no-rtl">
            <div>
                @if(Auth::check())
                    <img src="{{Auth::user()->avatar}}" class="rounded-circle user-avatar">
                @else
                    <div class="avatar-placeholder">
                        @include('elements.icon',['icon'=>'person-circle','variant'=>'xlarge'])
                    </div>
                @endif
            </div>
            <div class="pl-2 d-flex justify-content-center flex-column">
                @if(Auth::check())
                    <div class=""><span class=""><span>@</span>{{Auth::check() ? Auth::user()->username : '@username'}}</span></div>
                    <small class="p-0 m-0">{{trans_choice('fans', Auth::user()->fansCount, ['number'=>Auth::user()->fansCount])}} - {{trans_choice('following', Auth::user()->followingCount, ['number'=>Auth::user()->followingCount])}}</small>
                @endif
            </div>
        </div>
    </div>

    <ul class="list-unstyled menu-elements p-0">
        @if(Auth::check())
            <li class="{{Route::currentRouteName() == 'profile' && (request()->route("username") == Auth::user()->username) ? 'active' : ''}}">
                <a class="scroll-link d-flex align-items-center" href="{{route('profile',['username'=>Auth::user()->username])}}">
                    @include('elements.icon',['icon'=>'person-circle-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    {{__('My profile')}}</a>
            </li>
            <li class="{{Route::currentRouteName() == 'my.bookmarks' ? 'active' : ''}}">
                <a class="scroll-link d-flex align-items-center" href="{{route('my.bookmarks')}}">
                    @include('elements.icon',['icon'=>'bookmarks-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    {{__('Bookmarks')}}</a>
            </li>
            <li class="{{Route::currentRouteName() == 'my.lists.all' ? 'active' : ''}}">
                <a class="scroll-link d-flex align-items-center" href="{{route('my.lists.all')}}">
                    @include('elements.icon',['icon'=>'list','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    {{__('Lists')}}</a>
            </li>
            <li class="{{Route::currentRouteName() == 'my.settings' ? 'active' : ''}}">
                <a class="scroll-link d-flex align-items-center" href="{{route('my.settings')}}">
                    @include('elements.icon',['icon'=>'settings-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    {{__('Settings')}}</a>
            </li>
            <div class="menu-divider"></div>
        @endif
        <li>
            <a class="scroll-link d-flex align-items-center" href="{{route('pages.get',['slug'=>'help'])}}">
                @include('elements.icon',['icon'=>'help-circle-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                {{__('Help and support')}}</a>
        </li>
        @if(getSetting('site.allow_theme_switch'))
            <li>
                <a class="scroll-link d-flex align-items-center dark-mode-switcher" href="#">
                    @if(Cookie::get('app_theme') == 'dark')
                        @include('elements.icon',['icon'=>'contrast-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                        {{__('Light mode')}}
                    @else
                        @include('elements.icon',['icon'=>'contrast','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                        {{__('Dark mode')}}
                    @endif
                </a>
            </li>
        @endif
        @if(getSetting('site.allow_direction_switch'))
            <li>
                <a class="scroll-link d-flex align-items-center rtl-mode-switcher" href="#">
                    @include('elements.icon',['icon'=>'return-up-back','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    RTL</a>
            </li>
        @endif
        @if(getSetting('site.allow_language_switch'))
            <li>
                <a href="#otherSections" class="d-flex align-items-center" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" role="button" aria-controls="otherSections">
                    @include('elements.icon',['icon'=>'language','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    {{__('Language')}}
                </a>
                <ul class="collapse list-unstyled" id="otherSections">
                    @foreach(LocalesHelper::getAvailableLanguages() as $languageCode)
                        <li>
                            <a class="scroll-link d-flex align-items-center" href="{{route('language',['locale' => $languageCode])}}">{{__(LocalesHelper::getLanguageName($languageCode))}}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endif
        <div class="menu-divider"></div>
        <li>
            @if(Auth::check())
                <a class="scroll-link d-flex align-items-center pointer-cursor" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    @include('elements.icon',['icon'=>'log-out-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    {{__('Log out')}}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @else
                <a class="scroll-link d-flex align-items-center" href="{{route('login')}}">
                    @include('elements.icon',['icon'=>'log-in-outline','variant'=>'medium','centered'=>false,'classes'=>'mr-2'])
                    </i> {{__('Login')}}</a>
            @endif
        </li>
    </ul>
</nav>

<!-- Dark overlay -->
<div class="overlay"></div>
