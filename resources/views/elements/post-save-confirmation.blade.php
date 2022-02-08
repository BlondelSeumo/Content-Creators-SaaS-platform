<div class="modal fade" tabindex="-1" role="dialog" id="confirm-post-save">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Confirm post save')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Some attachments are still being uploaded.')}} {{__('Are you sure you want to continue?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm-post-save">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
