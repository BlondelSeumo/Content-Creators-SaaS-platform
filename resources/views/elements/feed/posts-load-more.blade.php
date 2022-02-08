@if(Cookie::get('app_feed_prev_page') && PostsHelper::isComingFromPostPage(request()->session()->get('_previous')))
    <div class="px-2 mt-3 reverse-paginate-btn pt-4 pt-md-0">
        <button class="btn btn-outline-primary btn-block" onclick="PostsPaginator.loadPreviousResults()">
            {{__('Load previous posts')}}
        </button>
    </div>
@endif
