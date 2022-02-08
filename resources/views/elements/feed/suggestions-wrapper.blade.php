<div class="suggestions-content">
    @if(count($profiles) > 0)
    <div class="swiper-container mySwiper">
        <div class="swiper-wrapper">
            @foreach ($profiles->chunk((int)getSetting('feed.feed_suggestions_card_per_page')) as $profilesSet)
                <div class="swiper-slide px-1">
                    @foreach ($profilesSet as $k => $profile)
                        @include('elements.feed.suggestion-card',['profile' => $profile])
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-center">
        <div class="swiper-pagination mt-4" ></div>
    </div>
    @else
        <p class="text-center">{{__('No suggestions available')}}</p>
    @endif
</div>
