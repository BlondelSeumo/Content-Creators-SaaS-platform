<div class="mobile-bottom-nav border-top z-index-3 py-1 neutral-bg">
    <div class="d-flex justify-content-between w-100 py-2 px-2">
        <a href="{{Auth::check() ? route('feed') : route('home')}}" class="h-pill h-pill-primary nav-link d-flex justify-content-between px-3 {{Route::currentRouteName() == 'feed' ? 'active' : ''}}">
            <div class="d-flex justify-content-center align-items-center">
                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                    @include('elements.icon',['icon'=>'home-outline','variant'=>'large'])
                </div>
            </div>
        </a>
        @if(Auth::check())
            <a href="{{route('my.notifications')}}" class="h-pill h-pill-primary nav-link d-flex justify-content-between px-3 {{Route::currentRouteName() == 'my.notifications' ? 'active' : ''}}">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'notifications-outline','variant'=>'large'])
                    </div>
                </div>
            </a>
            <a href="{{route('posts.create')}}" class="h-pill h-pill-primary nav-link d-flex justify-content-between px-3 {{Route::currentRouteName() == 'posts.create' ? 'active' : ''}}">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'add-circle-outline','variant'=>'large'])
                    </div>
                </div>
            </a>
            <a href="{{route('my.messenger.get')}}" class="h-pill h-pill-primary nav-link d-flex justify-content-between px-3 {{Route::currentRouteName() == 'my.messenger.get' ? 'active' : ''}}">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        @include('elements.icon',['icon'=>'chatbubble-outline','variant'=>'large'])
                    </div>
                </div>
            </a>
        @endif
        <a href="javascript:void(0)" class="open-menu h-pill h-pill-primary nav-link d-flex justify-content-between px-3">
            <div class="d-flex justify-content-center align-items-center">
                <div class="icon-wrapper d-flex justify-content-center align-items-center">
                    @if(Auth::check())
                        <img src="{{Auth::user()->avatar}}" class="rounded-circle user-avatar w-24">
                    @else
                        <div class="avatar-placeholder">
                            @include('elements.icon',['icon'=>'person-circle','variant'=>'large'])
                        </div>
                    @endif
                </div>
            </div>
        </a>
    </div>
</div>
