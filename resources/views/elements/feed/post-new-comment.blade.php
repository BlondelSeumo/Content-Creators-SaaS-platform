<div class="px-3 new-post-comment-area">
    <div class="d-flex justify-content-center align-items-center">
        <img class="rounded-circle" src="{{Auth::user()->avatar}}">
        <div class="input-group">
            <textarea name="message" class="form-control comment-textarea mx-3 comment-text" placeholder="{{__('Write a message..')}}"  onkeyup="textAreaAdjust(this)"></textarea>
            <div class="input-group-append z-index-3 d-flex align-items-center justify-content-center">
                <span class="h-pill h-pill-primary rounded mr-3 trigger" data-toggle="tooltip" data-placement="top" title="Like" >😊</span>
            </div>
            <span class="invalid-feedback pl-4 text-bold" role="alert"></span>
        </div>
        <div class="pl-2">
            <button class="btn btn-outline-primary btn-rounded-icon" onclick="Post.addComment({{$post->id}})">
                <div class="d-flex justify-content-center align-items-center">
                    @include('elements.icon',['icon'=>'paper-plane','variant'=>''])
                </div>
            </button>
        </div>
    </div>
</div>
