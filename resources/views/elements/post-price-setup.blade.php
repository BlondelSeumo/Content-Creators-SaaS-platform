<div class="modal fade" tabindex="-1" role="dialog" id="post-set-price-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Set post price')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Paid posts are locked for subscribers as well.')}}</p>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
                    </div>
                    <input id="post-price" type="text" class="form-control" name="text" required  placeholder="{{__('Post price')}}" value="{{$postPrice}}">
                    <span class="invalid-feedback" role="alert">
                        <strong>{{__('The locked post value must be higher than')}} {{config('app.site.currency_symbol')}}1.</strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white"   onclick="PostCreate.clearPostPrice()">{{__('Clear')}}</button>
                <button type="button" class="btn btn-primary" onclick="PostCreate.savePostPrice()">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
