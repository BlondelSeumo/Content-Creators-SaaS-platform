@if($type == 'generic')
    <div class="mt-3 ml-3">
        <h5 class="text-bold {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{__('Settings')}}</h5>
        <h6 class="mt-2 text-muted">{{__('Manage your account')}}</h6>
    </div>
@else
    <div class="ml-3">
        <h5 class="text-bold mt-0 mt-md-3 mb-0 {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{ ucfirst(__($activeSettingsTab))}}</h5>
        <h6 class="mt-2 text-muted">{{__($currentSettingTab['heading'])}}</h6>
    </div>
@endif
