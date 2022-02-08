<div class="modal fade" tabindex="-1" role="dialog" id="list-add-user-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Add user to list')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Chose the list you want to add the user into')}}</p>
                <div class="add-user-lists-wrapper">
                    @foreach($lists as $list)
                        <div class="form-check d-flex mb-3">
                            <input class="form-check-input input-group-lg pointer-cursor" data-listID="{{$list->id}}" type="checkbox" value="" {{ListsHelper::isMemberList($list->members, $user_id) ? 'checked' : ''}} id="list-{{$list->id}}">
                            <label class="form-check-label ml-3 mt-0 pointer-cursor" for="list-{{$list->id}}">
                                <h6 class="m-0 text-bold">{{__($list->name)}}</h6>
                                <span class="list-subtitle">{{trans_choice('members', count($list->members), ['number'=>count($list->members),])}} - {{trans_choice('posts', $list->posts_count, ['number'=>$list->posts_count])}}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white"  data-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>
