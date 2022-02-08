<div class="post-comment d-flex flex-row mb-3" data-commentID="{{$comment->id}}">
    <div class="">
        <img class="rounded-circle" src="{{$comment->author->avatar}}">
    </div>
    <div class="pl-3 w-100">
        <div class="d-flex flex-row justify-content-between">
            <div class="text-bold"><a href="#" class="text-dark-r">{{$comment->author->username}}</a></div>
            <div class="position-absolute separator">
             <span class="h-pill h-pill-primary rounded react-button {{PostsHelper::didUserReact($comment->reactions) ? 'active' : ''}}" data-toggle="tooltip" data-placement="top" title="Like" onclick="Post.reactTo('comment',{{$comment->id}})">
                 @include('elements.icon',['icon'=>'heart-outline'])
            </span>
            </div>
        </div>
        <div>
            {{$comment->message}}
            <div class="d-flex text-muted">
                <div>{{$comment->created_at->format('g:i A')}}</div>
                <div class="ml-2">
                    <span class="comment-reactions-label-count">{{count($comment->reactions)}}</span>
                    <span class="comment-reactions-label">{{trans_choice('likes',count($comment->reactions))}}</span>
                </div>
                <div class="ml-2"><a href="javascript:void(0)" onclick="Post.addReplyUser('{{$comment->author->username}}')" class="text-muted">{{__('Reply')}}</a></div>
            </div>
        </div>
    </div>
</div>
