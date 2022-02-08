@if(getSetting('social-login.facebook_client_id') || getSetting('social-login.twitter_client_id') || getSetting('social-login.google_client_id'))
    <div class=" d-flex align-items-center justify-content-center mt-3">
        <div class="socialLogin d-flex align-items-center">
            @if(getSetting('social-login.facebook_client_id'))
                <a class="mr-2 p-pill ml-2 pointer-cursor" href="{{url('',['socialAuth','facebook'])}}" rel="nofollow">
                    @include('elements.icon',['icon'=>'logo-facebook','variant'=>'small','classes' => 'opacity-8'])
                </a>
            @endif
            @if(getSetting('social-login.twitter_client_id'))
                <a class="mr-2 p-pill ml-2 pointer-cursor" href="{{url('',['socialAuth','twitter'])}}" rel="nofollow">
                    @include('elements.icon',['icon'=>'logo-twitter','variant'=>'small','classes' => 'opacity-8'])
                </a>
            @endif
            @if(getSetting('social-login.google_client_id'))
                <a class="mr-2 p-pill ml-2 pointer-cursor" href="{{url('',['socialAuth','google'])}}" rel="nofollow">
                    @include('elements.icon',['icon'=>'logo-google','variant'=>'small','classes' => 'opacity-8'])
                </a>
            @endif
        </div>
    </div>
@endif
