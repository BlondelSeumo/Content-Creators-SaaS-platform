<div class="modal fade " tabindex="-1" role="dialog" id="login-dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content p-2">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="block-user-label">{{__('Login to subscribe')}}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6 d-none d-md-block">
                        <div class="d-flex align-items-center justify-content-center card-wrapper">
                            @include('elements.vertical-member-card',['profile' => $user])
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 d-flex align-items-center pl-0">
                        @include('auth.modal-forms')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
