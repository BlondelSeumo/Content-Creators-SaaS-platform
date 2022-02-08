<div class="notifications-wrapper pt-4">
    @if(count($notifications))
        @foreach($notifications as $notification)
            @include('elements.notifications.notification-box', ['notification' => $notification])
            @if(!$loop->last)
                <hr class="my-2 ">
            @endif
        @endforeach
        <div class="d-flex flex-row-reverse mt-1 mb-1 mr-4">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="py-2">
            <div class="d-flex justify-content-center align-items-center">
                <div class="col-8 d-flex justify-content-center align-items-center">
                    <img src="{{asset('/img/no-notifications.svg')}}" class="no-notifications">
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <h5>{{__('Missing notifications')}}</h5>
            </div>
        </div>
    @endif
</div>
