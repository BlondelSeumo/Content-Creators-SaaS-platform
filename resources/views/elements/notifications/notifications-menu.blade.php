<nav class="nav nav-pills nav-justified {{$variant == 'desktop' ? 'notifications-nav' : 'notifications-nav-mobile'}}">
    <a class="nav-item nav-link text-bold {{!$activeType ? 'active' : ''}}" href="{{route('my.notifications')}}">
        <div class="d-flex justify-content-center">
            @include('elements.icon',['icon'=>'list-outline','centered'=>false,'variant'=>'medium'])
            <span class="d-none d-md-block ml-2">{{__('All')}}</span>
        </div>
    </a>
    @foreach($notificationTypes as $type)
        <a class="nav-item nav-link text-bold {{$activeType == $type ? 'active' : ''}}" href="{{route('my.notifications',['type' => $type])}}">
            <div class="d-flex justify-content-center">
                @switch($type)
                    @case('messages')
                    @include('elements.icon',['icon'=>'chatbubbles-outline','centered' => false,'variant'=>'medium'])
                    @break
                    @case('likes')
                    @include('elements.icon',['icon'=>'heart-outline','centered' => false,'variant'=>'medium'])
                    @break
                    @case('subscriptions')
                    @include('elements.icon',['icon'=>'people-circle-outline','centered' => false,'variant'=>'medium'])
                    @break
                    @case('tips')
                    @include('elements.icon',['icon'=>'gift-outline','centered' => false,'variant'=>'medium'])
                    @break
                    @case('promos')
                    @include('elements.icon',['icon'=>'sparkles-outline','centered' => false,'variant'=>'medium'])
                    @break
                @endswitch
                <span class="d-none d-md-block ml-2">{{__(ucfirst($type))}}</span>
            </div>
        </a>
    @endforeach
</nav>
