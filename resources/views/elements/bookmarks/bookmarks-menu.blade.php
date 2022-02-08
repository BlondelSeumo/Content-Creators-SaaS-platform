@if($variant == 'desktop')
    <div class="card-settings border-bottom">
        <div class="list-group list-group-sm list-group-flush">
            @foreach($bookmarkTypes as $route => $setting)
                <a href="{{route('my.bookmarks',['type'=>$route])}}" class="{{$activeTab == $route ? 'active' : ''}} list-group-item list-group-item-action d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        @include('elements.icon',['icon'=>$setting['icon'].'-outline','centered'=>'false','classes'=>'mr-3','variant'=>'medium'])
                        <span>{{__(ucfirst($route))}}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        @include('elements.icon',['icon'=>'chevron-forward-outline'])
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@else
    <div class="mt-3 inline-border-tabs text-bold">
        <nav class="nav nav-pills nav-justified">
            @foreach($bookmarkTypes as $route => $setting)
                <a class="nav-item nav-link {{$activeTab == $route ? 'active' : ''}}" href="{{route('my.bookmarks',['type'=>$route])}}">
                    <div class="d-flex justify-content-center">
                        @include('elements.icon',['icon'=>$setting['icon'].'-outline','centered'=>'false','variant'=>'medium'])
                    </div>
                </a>
            @endforeach
        </nav>
    </div>
@endif
