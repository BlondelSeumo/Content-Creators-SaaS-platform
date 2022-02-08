<div class="modal fade" tabindex="-1" role="dialog" id="messageModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title-default">{{ isset($user) ?   __('Send a new message to',['user' => $user->name]) :  __('Send a new message') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userMessageForm" role="form" autocomplete="off">
                    <div class="mfv-errorBox"></div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @if(!isset($user))
                        <div class="input-holder">
                            <select id="select-repo" name="receiverID" class="repositories form-control input-sm" placeholder="{{__('To...')}}"></select>
                        </div>
                        <br />
                    @else
                        <input type="hidden" name="receiverID" value="{{$user->id}}">
                    @endif
                    <div class="form-group focused">
                        <div class="input-holder">
                            <textarea class="form-control" name="message" placeholder="{{__('Your message')}}" id="messageText"></textarea>
                        </div>
                    </div>
                </form>
                <div class="modal-footer pl-0 pr-0 {{!isset($user) ? '' : 'pb-0'}}">
                    <button type="submit" onclick="messenger.{{isset($user) ? 'sendDMFromProfilePage' : 'createConversation'}}()"  class="btn-primary btn mr-0 new-conversation-label"> {{__('Send')}} </button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
