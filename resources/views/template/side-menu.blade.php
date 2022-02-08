<div class="side-menu px-1 px-md-2 px-lg-3">
    <div class="user-details mb-4 d-flex open-menu pointer-cursor flex-row-no-rtl">
        <div class="ml-0 ml-md-2">
            @if(Auth::check())
                <img src="{{Auth::user()->avatar}}" class="rounded-circle user-avatar">
            @else
                <div class="avatar-placeholder">
                    @include('elements.icon',['icon'=>'person-circle','variant'=>'xlarge text-muted'])
                </div>
            @endif
        </div>
        @if(Auth::check())
            <div class="d-none d-lg-block">
                <div class="pl-2 d-flex justify-content-center flex-column">
                    <div class="ml-2 d-flex flex-column">
                        <span class="text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{Auth::user()->name}}</span>
                        <span class="text-muted"><span>@</span>{{Auth::user()->username}}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <ul class="nav flex-column user-side-menu">
        <li class="nav-item ">
            <a href="{{Auth::check() ? route('feed') : route('home')}}" class="h-pill h-pill-primary nav-link {{Route::currentRouteName() == 'feed' ? 'active' : ''}} d-flex justify-content-between">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'home-outline','variant'=>'large'])
                    </div>
                    <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Home')}}</span>
                </div>
            </a>
        </li>
        @if(Auth::check())
            <li class="nav-item">
                <a href="{{route('my.notifications')}}" class="nav-link h-pill h-pill-primary {{Route::currentRouteName() == 'my.notifications' ? 'active' : ''}} d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'notifications-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Notifications')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.messenger.get')}}" class="nav-link {{Route::currentRouteName() == 'my.messenger.get' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'chatbubble-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Messages')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.bookmarks')}}" class="nav-link {{Route::currentRouteName() == 'my.bookmarks' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'bookmark-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Bookmarks')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.lists.all')}}" class="nav-link {{Route::currentRouteName() == 'my.lists.all' ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'list-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Lists')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('my.settings',['type'=>'subscriptions'])}}" class="nav-link {{Route::currentRouteName() == 'my.settings' &&  is_int(strpos(Request::path(),'subscriptions')) ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'people-circle-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('Subscriptions')}}</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('profile',['username'=>Auth::user()->username])}}" class="nav-link {{Route::currentRouteName() == 'profile' && (request()->route("username") == Auth::user()->username) ? 'active' : ''}} h-pill h-pill-primary d-flex justify-content-between">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            @include('elements.icon',['icon'=>'person-circle-outline','variant'=>'large'])
                        </div>
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('My profile')}}</span>
                    </div>
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a href="#" role="button" class="open-menu nav-link h-pill h-pill-primary text-muted d-flex justify-content-between">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'ellipsis-horizontal-circle-outline','variant'=>'large'])
                    </div>
                    <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate side-menu-label">{{__('More')}}</span>
                </div>
            </a>
        </li>
        @if(Auth::check())
            <li class="nav-item mt-2  mt-2 ">
                <a role="button" class="btn btn-round btn-primary btn-block " href="{{route('posts.create')}}">
                    <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate new-post-label">{{__('New post')}}</span>
                    <span class="d-block d-md-none d-flex align-items-center justify-content-center">@include('elements.icon',['icon'=>'add-circle-outline','variant'=>'medium','classes'=>'flex-shrink-0'])</span>
                </a>
            </li>
        @endif
    </ul>
</div>
