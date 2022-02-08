@if(session('success'))
    <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
        {{session('success')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-warning text-white font-weight-bold mt-2" role="alert">
        {{session('error')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(Auth::user()->verification && (Auth::user()->verification->rejectionReason && Auth::user()->verification->status === 'rejected' ) )
    <div class="alert alert-warning text-white font-weight-bold mt-2" role="alert">
        Your previous verification attempt was rejected for the following reason: "{{Auth::user()->verification->rejectionReason}}"
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form class="verify-form" action="{{route('my.settings.verify.save')}}" method="POST">
    @csrf
    <p>{{__('In order to get verified and receive your badge, please take care of the following steps:')}}</p>
    <div class="d-flex align-items-center mb-1 ml-4">
        @if(Auth::user()->email_verified_at)
            @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success mr-2'])
        @else
            @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning mr-2'])
        @endif
        {{__('Confirm your email address.')}}
    </div>
    <div class="d-flex align-items-center mb-1 ml-4">
        @if(Auth::user()->birthdate)
            @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success mr-2'])
        @else
            @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning mr-2'])
        @endif
        {{__('Set your birthdate.')}}
    </div>
    <div class="d-flex align-items-center ml-4">
        @if((Auth::user()->verification && Auth::user()->verification->status == 'verified'))
            @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'medium', 'classes'=>'text-success mr-2']) {{__('Upload a Goverment issued ID card.')}}
        @else
            @if(!Auth::user()->verification || (Auth::user()->verification && Auth::user()->verification->status !== 'verified' && Auth::user()->verification->status !== 'pending'))
                @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'medium', 'classes'=>'text-warning mr-2']) {{__('Upload a Goverment issued ID card.')}}
            @else
                @include('elements.icon',['icon'=>'time-outline','variant'=>'medium', 'classes'=>'text-primary mr-2']) {{__('Identity check in progress.')}}
            @endif
        @endif
    </div>
    @if((!Auth::user()->verification || (Auth::user()->verification && Auth::user()->verification->status !== 'verified' && Auth::user()->verification->status !== 'pending')) )
        <h5 class="mt-5 mb-3">{{__("Complete your verification")}}</h5>
        <p class="mb-1 mt-2">{{__("Please attach clear photos of your ID card back and front side.")}}</p>
        <div class="dropzone-previews dropzone w-100 ppl-0 pr-0 pt-1 pb-1 border rounded"></div>
        <div class="d-flex flex-row-reverse">
            <button class="btn btn-primary mt-2">{{__("Submit")}}</button>
        </div>
    @endif
    @if(Auth::user()->email_verified_at && Auth::user()->birthdate && (Auth::user()->verification && Auth::user()->verification->status == 'verified'))
        <p class="mt-3">{{__("Your info looks good, you're all set to post new content!")}}</p>
        @endif
</form>
@include('elements.uploaded-file-preview-template')
