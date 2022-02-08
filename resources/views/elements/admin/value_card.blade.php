<div class="{{ $size }}">
    <div class="card shadow rounded border-0 h-100">
        <div class="card-body">
            <form name="{{ $name }}" action="{{ route($route) }}" method="get">
                <div class="row">
                    <div class="col-md-10 col-xs-12">
                        <div class="text-muted font-weight-bolder">{{ $title }}</div>
                    </div>
                    <div class="col-md-2 col-xs-12">
                        <input type="hidden" name="function" value="{{ $form['function'] }}">
                        <select name="range" class="form-control form-control-sm card-value">
                            @foreach($form['ranges'] as $range)
                                <option value="{{ $range }}" @if($form['range'] == $range) selected @endif>{{ $range }} {{ $range == 1 ? $form['trans'][0] : $form['trans'][1] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="h2 mt-3" data-card-value></div>
                        <div class="mb-2 mt-3" data-card-loading>
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>

                        <div class="text-muted font-weight-medium">
                    <span class="d-none text-success" data-card-status-increase>
                        <div class="d-flex align-items-center">
                            <div class="icon voyager-angle-up"></div>
                            <div class="d-flex">
                                <span class="chart-trend d-flex align-items-center text-success mr-2"></span> <span data-card-increase-growth></span>% {{ __('Increase') }}
                            </div>
                        </div>
                    </span>
                            <span class="d-none text-danger" data-card-status-decrease>
                                <div class="d-flex align-items-center">
                                <div class="icon voyager-angle-down"></div>
                                <div class="d-flex">
                                    <span class="chart-trend d-flex align-items-center text-danger mr-2"></span> <span data-card-decrease-growth></span>% {{ __('Decrease') }}
                                </div>
                           </div>
                    </span>
                            <span class="d-none" data-card-status-constant>{{ __('Constant') }}</span>
                            <span class="d-none" data-card-status-npd>{{ __('No prior data') }}</span>
                            <span class="d-none" data-card-status-ncd>{{ __('No current data') }}</span>
                            <span class="d-none text-danger" data-card-status-error></span>
                            <span data-card-status-loading>{{ __('Loading...') }}</span>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>
<script>
    "use strict";
    document.addEventListener("DOMContentLoaded", function() {
        getCardValue(document.querySelector('form[name="{{ $name }}"]'), '{{ __('thousands_separator') }}');
        document.querySelector('form[name="{{ $name }}"] select[name="range"]').addEventListener('change' , function() {
            getCardValue(document.querySelector('form[name="{{ $name }}"]'), '{{ __('thousands_separator') }}');
        });
    });
</script>
