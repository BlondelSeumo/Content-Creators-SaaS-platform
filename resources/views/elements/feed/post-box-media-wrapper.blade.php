@if( !(!$isGallery && AttachmentHelper::getAttachmentType($attachment->type) == 'video'))
    <a href="{{$attachment->path}}" rel="mswp" title="">
@endif

    @if($isGallery)
        @if(AttachmentHelper::getAttachmentType($attachment->type) == 'image')
                <div class="post-media-image" style="background-image: url('{{$attachment->path}}');">
                </div>
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'video')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <video class="video-preview w-100" src="{{$attachment->path}}" controls></video>
                </div>
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'audio')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <audio class="video-preview w-75" src="{{$attachment->path}}" controls></audio>
                </div>
            @endif
        @else
            @if(AttachmentHelper::getAttachmentType($attachment->type) == 'image')
                <img src="{{$attachment->path}}" draggable="false" alt="" class="img-fluid rounded-0 w-100">
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'video')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <video class="video-preview w-100" src="{{$attachment->path}}" controls></video>
                </div>
            @elseif(AttachmentHelper::getAttachmentType($attachment->type) == 'audio')
                <div class="video-wrapper h-100 w-100 d-flex justify-content-center align-items-center">
                    <audio class="video-preview w-75" src="{{$attachment->path}}" controls></audio>
                </div>
            @endif
        @endif

@if( !(!$isGallery && AttachmentHelper::getAttachmentType($attachment->type) == 'video'))
    </a>
@endif
