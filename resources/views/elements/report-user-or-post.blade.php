<div class="modal fade" tabindex="-1" role="dialog" id="report-user-post">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Report user or post')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <label for="reasonExamples">{{__('Reason')}}</label>
                    <select id="reasonExamples" class="form-control">
                        @foreach($reportStatuses as $status)
                            <option value="{{$status}}">{{$status}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="control-group mt-2">
                    <label for="exampleTextarea">{{__('Details')}}</label>
                    <textarea class="form-control" id="post_report_details" rows="2"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning submit-report-button">{{__('Report')}}</button>
            </div>
        </div>
    </div>
</div>
