<div class="py-2 notification-box  pl-3 pl-md-4">
    <div class="d-flex flex-row my-1">
        @if($notification->fromUser)
        <div class="">
            <img class="rounded-circle" src="{{$notification->fromUser->avatar}}" alt="{{$notification->fromUser->username}}">
        </div>
        @endif
        <div class="pl-3 w-100">
            <div class="d-flex flex-row justify-content-between">
                @if($notification->fromUser)
                <div class="d-flex flex-column">
                    <h6 class="text-bold  m-0 p-0"><a href="{{route('profile',['username'=>$notification->fromUser->username])}}" class="text-dark-r">{{$notification->fromUser->name}}</a></h6>
                    <div class="text-bold"><a href="{{route('profile',['username'=>$notification->fromUser->username])}}" class="text-muted">{{'@'}}{{$notification->fromUser->username}}</a></div>
                </div>
                @endif
                <div class="position-absolute separator">
                </div>
            </div>
            <div>
                <div class="my-1 text-break pr-3">
                    @switch($notification->type)
                        @case(\App\Model\Notification::NEW_TIP)
                        {{$notification->transaction->sender->name}} {{__("sent you a tip of")}} {{$notification->transaction->amount}}  {{$notification->transaction->currency}}.
                        @break
                        @case(\App\Model\Notification::NEW_REACTION)
                        @if($notification->post_id)
                            {{__(":name liked your post",['name'=>$notification->post->user->name])}}
                        @endif
                        @if($notification->post_comment_id)
                            {{__(":name liked your comment",['name'=>$notification->postComment->author->name])}}
                        @endif
                        @break
                        @case(\App\Model\Notification::NEW_COMMENT)
                        {{__(':name added a new comment on your post',['name'=>$notification->fromUser->name])}}
                        @break
                        @case(\App\Model\Notification::NEW_SUBSCRIPTION)
                        {{__("A new user subscribed to your profile")}}
                        @break
                        @case(\App\Model\Notification::WITHDRAWAL_ACTION)
                        {{
                            __("Withdrawal processed",[
                                            'currencySymbol' => \App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol(),
                                            'amount' => $notification->withdrawal->amount,
                                            'status' =>  $notification->withdrawal->status,
                                        ])

                        }}
                        @break
                        @case(\App\Model\Notification::NEW_MESSAGE)
                        {{__("Send you a message: `:message`",['message'=>$notification->userMessage->message])}}
                        @break
                    @endswitch

                </div>
                <div class="d-flex text-muted">
                    <div>{{ \Carbon\Carbon::parse($notification->created_at)->diffForhumans(Carbon\Carbon::now()) }} </div>
                </div>
            </div>
        </div>
    </div>
</div>
