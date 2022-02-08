<div class="suggestion-box card text-white mb-2 border-0 rounded" data-memberuserid="{{$profile->id}}">
    <div style="background: url({{$profile->cover}});" class="card-img suggestion-header-bg"></div>
    <div class="card-img-overlay p-0">
        <div class="h-100 w-100 p-0 m-0 position-absolute z-index-0">
            <div class="h-50">
            </div>
            <div class="h-50 w-100 half-bg d-flex"></div>
        </div>
        <div class="card-text w-100 h-100 d-flex">

            <div class="d-flex align-items-center justify-content-center px-3 z-index-3">
                <img src="{{$profile->avatar}}" class="avatar rounded-circle"  />
            </div>

            <div class="w-100 z-index-3 text-truncate">
                <div class="h-50 d-flex flex-row-reverse pr-1">
                    @if(isset($isListMode))
                    <span class="h-pill h-pill-accent rounded mt-1 suggestion-card-btn" data-toggle="tooltip" data-placement="bottom" title="" onclick="Lists.showListMemberRemoveModal({{$profile->id}})" data-original-title="{{__('Delete')}}">
                        @include('elements.icon',['icon'=>'trash-outline','variant'=>'medium'])
                    </span>
                    @endif
                </div>
                <div class="h-50 w-100 z-index-3 d-flex flex-column justify-content-center text-truncate pr-2">
                    <div class="m-0 h6 text-truncate"><a href="{{route('profile',['username'=>$profile->username])}}" class="text-white d-flex align-items-center">{{$profile->name}}
                        @if($profile->email_verified_at && $profile->birthdate && ($profile->verification && $profile->verification->status == 'verified'))
                            <span data-toggle="tooltip" data-placement="top" title="{{__('Verified user')}}">
                                @include('elements.icon',['icon'=>'checkmark-circle-outline','centered'=>true,'classes'=>'ml-1'])
                            </span>
                        @endif
                        </a></div>
                    <div class="m-0 text-truncate"><span>@</span><a href="{{route('profile',['username'=>$profile->username])}}" class="text-white">{{$profile->username}}</a></div>
                </div>
            </div>

        </div>
    </div>
</div>
