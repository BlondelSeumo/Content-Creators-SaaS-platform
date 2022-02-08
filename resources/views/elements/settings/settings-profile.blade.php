@if(!Auth::user()->email_verified_at) @include('elements.resend-verification-email-box') @endif

<form method="POST" action="{{route('my.settings.profile.save',['type'=>'profile'])}}">
    @csrf
    {{--   Dummy dropzone file preview template  --}}
    <div class="dz-preview dz-file-preview d-none">
        <div class="dz-details d-none">
            <img data-dz-thumbnail />
        </div>
        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
        <div class="dz-success-mark"><span>✔</span></div>
        <div class="dz-error-mark"><span>✘</span></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
    </div>
    <div class="mb-4">
        <div class="">
            <div class="card profile-cover-bg">
                <img class="card-img-top centered-and-cropped" src="{{Auth::user()->cover}}">
                <div class="card-img-overlay d-flex justify-content-center align-items-center">
                    <div class="actions-holder d-none">

                    <div class="d-flex">
                        <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button" data-toggle="tooltip" data-placement="top" title="{{__('Upload cover image')}}">
                             @include('elements.icon',['icon'=>'image','variant'=>'medium'])
                        </span>
                        <span class="h-pill h-pill-accent pointer-cursor" onclick="ProfileSettings.removeUserAsset('cover')" data-toggle="tooltip" data-placement="top" title="{{__('Remove cover image')}}">
                            @include('elements.icon',['icon'=>'close','variant'=>'medium'])
                        </span>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="card avatar-holder">
                <img class="card-img-top" src="{{Auth::user()->avatar}}">
                <div class="card-img-overlay d-flex justify-content-center align-items-center">
                    <div class="actions-holder d-none">
                        <div class="d-flex">
                        <span class="h-pill h-pill-accent pointer-cursor mr-1 upload-button" data-toggle="tooltip" data-placement="top" title="{{__('Upload avatar')}}">
                            @include('elements.icon',['icon'=>'image','variant'=>'medium'])
                        </span>
                            <span class="h-pill h-pill-accent pointer-cursor" onclick="ProfileSettings.removeUserAsset('avatar')" data-toggle="tooltip" data-placement="top" title="{{__('Remove avatar')}}">
                             @include('elements.icon',['icon'=>'close','variant'=>'medium'])
                        </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
            {{session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="form-group">
        <label for="username">{{__('Username')}}</label>
        <input class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" aria-describedby="emailHelp" value="{{Auth::user()->username}}">
        @if($errors->has('username'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('username')}}</strong>
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="name">{{__('Full name')}}</label>
        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" name="name" aria-describedby="emailHelp" value="{{Auth::user()->name}}">
        @if($errors->has('name'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('name')}}</strong>
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="bio">{{__('Bio')}}</label>
        <textarea class="form-control {{ $errors->has('bio') ? 'is-invalid' : '' }}" id="bio" name="bio" rows="3" spellcheck="false">{{Auth::user()->bio}}</textarea>
        @if($errors->has('bio'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('bio')}}</strong>
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="birthdate">{{__('Birthdate')}}</label>
        <input type="date" class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location" name="birthdate" aria-describedby="emailHelp"  value="{{Auth::user()->birthdate}}">
        @if($errors->has('birthdate'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('birthdate')}}</strong>
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="location">{{__('Location')}}</label>
        <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location" name="location" aria-describedby="emailHelp"  value="{{Auth::user()->location}}">
        @if($errors->has('location'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('location')}}</strong>
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="website" value="{{Auth::user()->website}}">{{__('Website URL')}}</label>
        <input type="url" class="form-control {{ $errors->has('website') ? 'is-invalid' : '' }}" id="website" name="website" aria-describedby="emailHelp" value="{{Auth::user()->website}}">
        @if($errors->has('website'))
            <span class="invalid-feedback" role="alert">
                <strong>{{$errors->first('website')}}</strong>
            </span>
        @endif
    </div>
    <button class="btn btn-primary btn-block rounded mr-0" type="submit">{{__('Save')}}</button>
</form>
