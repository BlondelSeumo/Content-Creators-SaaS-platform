@if(count($posts))
    @foreach($posts as $post)
        @include('elements.feed.post-box')
        <hr>
    @endforeach
    @include('elements.report-user-or-post',['reportStatuses' => ListsHelper::getReportTypes()])
    @include('elements.feed.post-delete-dialog')
    @include('elements.feed.post-list-management')
    @include('elements.photoswipe-container')
@else
    <div class="d-flex justify-content-center align-items-center">
        <div class="col-10">
            <img src="{{asset('/img/no-content-available.svg')}}">
        </div>
    </div>
    <div class="d-flex justify-content-center align-items-center">
        <h5 class="text-center mb-2 mt-2">{{__('No posts available')}}</h5>
    </div>
@endif
