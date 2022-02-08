<div class="modal fade" tabindex="-1" role="dialog" id="list-update-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Create a new list')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="">
                    <input id="list-name" type="text" class="form-control" name="text" required  placeholder="{{__('List name')}}">
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{__('Please enter a list name.')}}</strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="Lists.updateList('{{$mode}}');">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
