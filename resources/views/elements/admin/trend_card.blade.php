<div class="{{ $size }}">
    <div class="card border-0 shadow rounded h-100">
        <div class="card-body">
            <form name="{{ $name }}" action="{{ route($route) }}" method="get">
                <div class="row">
                    <div class="col-md-10 col-xs-12">
                        <div class="text-muted font-weight-bolder">{{ $title }}</div>
                    </div>
                    <div class="col-md-2 col-xs-12">
                        <input type="hidden" name="function" value="{{ $form['function'] }}">
                        <input type="hidden" name="unit" value="{{ $form['unit'] }}">
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
                            <span class="d-none text-danger" data-card-status-error></span>
                            <span data-card-status-loading>{{ __('Loading...') }}</span>
                        </div>

                        <div class="ml-n3 mr-n3 mb-n3" style="height: {{ $chart['size'] }}px" data-card-chart>
                            <canvas id="{{ $name }}"></canvas>
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
        const ctx = document.querySelector('#{{ $name }}').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, {{ $chart['size'] }});
        gradient.addColorStop(0, '{{ $chart['color_start'] }}');
        gradient.addColorStop(1, '{{ $chart['color_stop'] }}');
        const {{ $name }} = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: '{{ $title }}',
                    backgroundColor : gradient,
                    borderColor: '{{ $chart['border_color'] }}',
                    data: []
                }]
            },
            options: {
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return parseFloat(tooltipItem.value).format(0, 3, '{{ __('thousands_separator') }}').toString();
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }],
                },
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right:10,
                        left:10,
                        top:5,
                        bottom:5,
                    }
                }
            }
        });
        getCardTrend(document.querySelector('form[name="{{ $name }}"]'), {{ $name }}, '{{ __('thousands_separator') }}', {{ $chart['total'] }});
        document.querySelector('form[name="{{ $name }}"] select[name="range"]').addEventListener('change' , function() {
            getCardTrend(document.querySelector('form[name="{{ $name }}"]'), {{ $name }}, '{{ __('thousands_separator') }}', {{ $chart['total'] }});
        });
    });
</script>
