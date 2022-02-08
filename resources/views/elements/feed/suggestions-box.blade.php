<div class="suggestions-box border rounded-lg px-2 py-4">
    <div class="d-flex justify-content-between suggestions-header mb-3 px-1">
        <h6 class="text-uppercase">{{__('Suggestions')}}</h6>
        <div class="d-flex">
            <div class="d-flex">
            </div>
            <div class="d-flex">
                <span class="mr-2 mr-xl-3 d-none d-lg-block pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Free account only')}}" onclick="SuggestionsSlider.loadSuggestions({'free':true});">
                    @include('elements.icon',['icon'=>'pricetag-outline','variant'=>'medium','centered'=>false])
                </span>
                <span class="mr-2 mr-xl-3 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Refresh suggestions')}}" onclick="SuggestionsSlider.loadSuggestions()">
                   @include('elements.icon',['icon'=>'refresh','variant'=>'medium','centered'=>false])
                </span>
{{--                @if(count($profiles) > 0)--}}
{{--                <div class="d-flex flex-row">--}}
{{--                    <span class="mr-2 mr-xl-3 suggestions-prev-slide" data-toggle="tooltip" data-placement="top" title="{{__('Previous page')}}">--}}
{{--                        @include('elements.icon',['icon'=>'chevron-back-outline','variant'=>'medium','centered'=>false])--}}
{{--                    </span>--}}
{{--                    <span class="mr-2 mr-xl-3 suggestions-next-slide" data-toggle="tooltip" data-placement="top" title="{{__('Next page')}}">--}}
{{--                       @include('elements.icon',['icon'=>'chevron-forward-outline','variant'=>'medium','centered'=>false])--}}
{{--                   </span>--}}
{{--                </div>--}}
{{--                @endif--}}
            </div>
        </div>
    </div>
    @include('elements.feed.suggestions-wrapper',['profiles'=>$profiles])
</div>
