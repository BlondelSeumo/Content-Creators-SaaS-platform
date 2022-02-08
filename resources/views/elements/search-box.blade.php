<form action="{{ route('search.get')}}" class="search-box-wrapper w-100" method="GET">
    <div class="input-group input-group-seamless-append">
        <input type="text" class="form-control shadow-none" aria-label="Text input with dropdown button" placeholder="Search" name="query" value="{{isset($searchTerm) && $searchTerm ? $searchTerm : ''}}">
        <div class="input-group-append">
            <span class="input-group-text">
                <span class="h-pill h-pill-primary rounded file-upload-button" onclick="submitSearch();">
                    @include('elements.icon',['icon'=>'search'])
                </span>
            </span>
        </div>
    </div>
    <input type="hidden" name="filter" value="{{isset($activeFilter) && $activeFilter !== false ? $activeFilter : 'top'}}" />
</form>
