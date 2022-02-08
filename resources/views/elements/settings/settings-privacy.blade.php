<div class="form-group ">
    <div class="custom-control custom-switch custom-switch">
        <input type="checkbox" class="custom-control-input" id="public_profile" {{Auth::user()->public_profile ? 'checked' : ''}}>
        <label class="custom-control-label" for="public_profile">{{__('Is public account')}}</label>
    </div>
    <div class="mt-3">
        <span>{{__('Having your profile set to private means:')}}</span>
        <ul class="mt-1 mb-2">
            <li>{{__('It will be hidden for search engines and unregistered users entirely.')}}</li>
            <li>{{__('It will also be generally hidden from suggestions and user searches on our platform.')}}</li>
        </ul>
    </div>
</div>
