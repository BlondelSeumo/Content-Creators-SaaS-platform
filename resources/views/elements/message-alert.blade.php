@if(Session::has('error') || Session::has('success') || Session::has('warning'))
    <div class="px-2 pb-2 pt-2">
@endif
@if(Session::has('error'))
    <div class="error-message alert text-white alert-danger alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ Session::get("error") }}
    </div>
@endif

@if(Session::has('success'))
    <div class="alert text-white alert-dismissible alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ Session::get("success") }}
    </div>
@endif

@if(Session::has('warning'))
    <div class="alert text-white alert-dismissible alert-warning">
        <button type="button" class="close" data-dismiss="warning">×</button>
        {{ Session::get("warning") }}
    </div>
@endif

@if(Session::has('error') || Session::has('success') || Session::has('warning'))
    </div>
@endif
