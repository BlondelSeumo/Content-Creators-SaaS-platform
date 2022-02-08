<div class="post-box" data-postID="{{$post->id}}">
    <div class="post-header pl-3 pr-3">
        <div class="d-flex">
            <div class="avatar-wrapper">
                <img class="avatar rounded-circle" src="{{$post->user->avatar}}">
            </div>
            <div class="post-details pl-2 w-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-bold"><a href="{{route('profile',['username'=>$post->user->username])}}" class="text-dark-r">{{$post->user->name}}</a></div>
                        <div><a href="{{route('profile',['username'=>$post->user->username])}}" class="text-dark-r text-hover"><span>@</span>{{$post->user->username}}</a></div>
                    </div>

                    <div class="d-flex">
                        <div class="pr-3 pr-md-3"><a class="text-dark-r text-hover" onclick="PostsPaginator.goToPostPageKeepingNav({{$post->id}},{{$post->postPage}},'{{route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username])}}')" href="javascript:void(0)">{{$post->created_at->diffForHumans(null,false,true)}}</a></div>
                        <div class="dropdown {{Cookie::get('app_rtl') == 'rtl' ? 'dropright' : 'dropleft'}}">
                            <a class="btn btn-sm text-dark-r text-hover btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle px-2 py-1 m-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                @include('elements.icon',['icon'=>'ellipsis-horizontal-outline'])
                            </a>
                            <div class="dropdown-menu">
                                <!-- Dropdown menu links -->
                                <a class="dropdown-item" href="javascript:void(0)" onclick="shareOrCopyLink('{{route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username])}}')">{{__('Copy post link')}}</a>
                                @if(Auth::check())
                                    <a class="dropdown-item bookmark-button {{PostsHelper::isPostBookmarked($post->bookmarks) ? 'active' : ''}}" href="javascript:void(0);" onclick="Post.togglePostBookmark({{$post->id}});">{{PostsHelper::isPostBookmarked($post->bookmarks) ? __('Remove the bookmark') : __('Bookmark this post') }} </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="Lists.showListManagementConfirmation('{{'unfollow'}}', {{$post->user->id}});">{{__('Unfollow')}}</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="Lists.showListManagementConfirmation('{{'block'}}', {{$post->user->id}});">{{__('Block')}}</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="Lists.showReportBox({{$post->user->id}},{{$post->id}});">{{__('Report')}}</a>
                                    @if(Auth::check() && Auth::user()->id == $post->user->id)
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{route('posts.edit',['post_id'=>$post->id])}}">{{__('Edit post')}}</a>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="Post.confirmPostRemoval({{$post->id}});">{{__('Delete post')}}</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="post-content mt-3  pl-3 pr-3">
        <p class="text-truncate">{{$post->text}}</p>
    </div>

    @if(count($post->attachments))
        <div class="post-media">
            @if($post->isSubbed)
                @if(Auth::user()->id !== $post->user_id && $post->price > 0 && !PostsHelper::hasUserUnlockedPost($post->postPurchases))
                    @include('elements.feed.post-locked',['type'=>'post','post'=>$post])
                @else
                    @if(count($post->attachments) > 1)
                        <div class="swiper-container mySwiper pointer-cursor">
                            <div class="swiper-wrapper">
                                @foreach($post->attachments as $attachment)
                                    <div class="swiper-slide">
                                        @include('elements.feed.post-box-media-wrapper',[
                                            'attachment' => $attachment,
                                            'isGallery' => true,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button swiper-button-next p-pill-white">@include('elements.icon',['icon'=>'chevron-forward-outline'])</div>
                            <div class="swiper-button swiper-button-prev p-pill-white">@include('elements.icon',['icon'=>'chevron-back-outline'])</div>
                            <div class="swiper-pagination"></div>
                        </div>
                    @else
                        @include('elements.feed.post-box-media-wrapper',[
                            'attachment' => $post->attachments[0],
                            'isGallery' => false,
                        ])
                    @endif
                @endif
            @else
                @include('elements.feed.post-locked',['type'=>'subscription',])
            @endif
        </div>
    @endif
    <div class="post-footer mt-3 pl-3 pr-3">
        <div class="footer-actions d-flex justify-content-between">
            <div class="d-flex">
                {{-- Likes --}}
                @if($post->isSubbed)
                    <div class="h-pill h-pill-primary mr-1 rounded react-button {{PostsHelper::didUserReact($post->reactions) ? 'active' : ''}}" data-toggle="tooltip" data-placement="top" title="{{__('Like')}}" onclick="Post.reactTo('post',{{$post->id}})">
                        @include('elements.icon',['icon'=>'heart-outline', 'variant' => 'medium'])
                    </div>
                @else
                    <div class="h-pill h-pill-primary mr-1 rounded react-button disabled">
                        @include('elements.icon',['icon'=>'heart-outline', 'variant' => 'medium'])
                    </div>
                @endif
                {{-- Comments --}}
                @if(Route::currentRouteName() != 'posts.get')
                    @if($post->isSubbed)
                        <div class="h-pill h-pill-primary mr-1 rounded" data-toggle="tooltip" data-placement="top" title="{{__('Show comments')}}" onClick="Post.showPostComments({{$post->id}},6)">
                            @include('elements.icon',['icon'=>'chatbubble-outline', 'variant' => 'medium'])
                        </div>
                    @else
                        <div class="h-pill h-pill-primary mr-1 disabled rounded">
                            @include('elements.icon',['icon'=>'chatbubble-outline', 'variant' => 'medium'])
                        </div>
                    @endif
                @endif
                {{-- Tips --}}
                @if(\Illuminate\Support\Facades\Auth::user() != null && $post->user->id != \Illuminate\Support\Facades\Auth::user()->id)
                    @if($post->isSubbed)
                        <div class="h-pill h-pill-primary send-a-tip"
                             data-toggle="modal"
                             data-target="#checkout-center"
                             data-post-id="{{$post->id}}"
                             data-type="tip"
                             @if(Auth::check())
                             data-first-name="{{Auth::user()->first_name}}"
                             data-last-name="{{Auth::user()->last_name}}"
                             data-billing-address="{{Auth::user()->billing_address}}"
                             data-country="{{Auth::user()->country}}"
                             data-city="{{Auth::user()->city}}"
                             data-state="{{Auth::user()->state}}"
                             data-postcode="{{Auth::user()->postcode}}"
                             data-available-credit="{{Auth::user()->wallet->total}}"
                             @endif
                             data-username="{{$post->user->username}}"
                             data-name="{{$post->user->name}}"
                             data-avatar="{{$post->user->avatar}}"
                             data-recipient-id="{{$post->user_id}}">
                            <div class=" d-flex align-items-center">
                                @include('elements.icon',['icon'=>'gift-outline', 'variant' => 'medium'])
                                <div class="ml-1 d-none d-lg-block"> {{__('Send a tip')}} </div>
                            </div>
                        </div>
                    @else
                        <div class="h-pill h-pill-primary send-a-tip disabled">
                            <div class=" d-flex align-items-center">
                                @include('elements.icon',['icon'=>'gift-outline', 'variant' => 'medium'])
                                <div class="ml-1 d-none d-md-block"> {{__('Send a tip')}} </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="mt-0 d-flex align-items-center justify-content-center post-count-details">
                <span class="ml-2-h">
                    <strong class="text-bold post-reactions-label-count">{{count($post->reactions)}}</strong>
                    <span class="post-reactions-label">{{trans_choice('likes', count($post->reactions))}}</span>
                </span>
                @if($post->isSubbed)
                    <span class="ml-2-h d-none d-lg-block">
                    <a href="{{Route::currentRouteName() != 'posts.get' ? route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username]) : '#comments'}}" class="text-dark-r text-hover">
                        <strong class="post-comments-label-count">{{count($post->comments)}}</strong>
                       <span class="post-comments-label">
                        {{trans_choice('comments',  count($post->comments))}}
                       </span>
                    </a>
                </span>
                @else
                    <span class="ml-2-h d-none d-lg-block">
                        <strong class="post-comments-label-count">{{count($post->comments)}}</strong>
                       <span class="post-comments-label">
                        {{trans_choice('comments',  count($post->comments))}}
                       </span>
                </span>
                @endif
                <span class="ml-2-h d-none d-lg-block">
                    <strong class="post-tips-label-count">{{$post->tips_count}}</strong>
                    <span class="post-tips-label">{{trans_choice('tips',['number' => count($post->comments)])}}</span>
                </span>
            </div>
        </div>
    </div>

    @if($post->isSubbed)
        <div class="post-comments d-none" {{Route::currentRouteName() == 'posts.get' ? 'id="comments"' : ''}}>
            <hr>

            <div class="px-3 post-comments-wrapper">
                <div class="comments-loading-box">
                    @include('elements.preloading.messenger-contact-box',['limit'=>1])
                </div>
            </div>
            <div class="show-all-comments-label pl-3 d-none">
                @if(Route::currentRouteName() != 'posts.get')
                    <a href="javascript:void(0)" onclick="PostsPaginator.goToPostPageKeepingNav({{$post->id}},{{$post->postPage}},'{{route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username])}}')">{{__('Show more')}}</a>
                @else
                    <a onClick="CommentsPaginator.loadResults({{$post->id}});" href="javascript:void(0);">{{__('Show more')}}</a>
                @endif
            </div>
            <div class="no-comments-label pl-3 d-none">
                {{__('No comments yet.')}}
            </div>
            @if(Auth::check())
                <hr>
                @include('elements.feed.post-new-comment')
            @endif
        </div>
    @endif

</div>
