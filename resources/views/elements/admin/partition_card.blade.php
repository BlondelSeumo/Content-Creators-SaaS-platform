<div class="{{ $size }}">
    <div class="card border-0 shadow rounded h-100">
        <div class="card-body">
            <form name="{{ $name }}" action="{{ route($route) }}" method="get">
                <div class="row">
                    <div class="col-md-8 col-xs-12">
                        <div class="text-muted font-weight-bolder">{{ $title }}</div>

                        <div class="mt-3 d-none text-muted font-weight-medium" data-card-legend>
                            <div class="row mb-2 d-none" data-legend-placeholder>
                                <div class="col-md-6 mb-1"><span class="chart-legend mr-2 rounded d-inline-block" style></span><span data-legend-name></span></div>
                                <div class="col-md-6 mb-1"><span class="text-muted" data-legend-value></span></div>
                            </div>
                        </div>

                        <div class="mb-2 mt-3" data-card-loading>
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>

                        <div class="text-muted font-weight-medium">
                            <span class="d-none text-danger" data-card-status-error></span>
                            <span data-card-status-loading>{{ __('Loading...') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="d-flex justify-content-center align-items-center">
                            <input type="hidden" name="api_token" value="">
                            <input type="hidden" name="function" value="{{ $form['function'] }}">

                            <div style="height: {{ $chart['size'] }}px; width: {{ $chart['size'] }}px" data-card-chart>
                                <canvas id="{{ $name }}"></canvas>
                            </div>
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
        const {{ $name }} = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    borderWidth: 0
                }]
            },
            options: {
                legend: false,
                cutoutPercentage: 65,
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
                    margin: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    }
                },
                tooltips: {
                    callbacks: {
                        title: function(tooltipItem, data) {
                            return data.labels[tooltipItem[0].index];
                        },
                        label: function(tooltipItem, data) {
                            let values = data.datasets[0].data;
                            let value = parseFloat(data.datasets[0].data[tooltipItem.index]);
                            let total = 0;  // Variable to hold your total
                            for(let i = 0, len = values.length; i < len; i++) {
                                total += parseFloat(values[i]);
                            }
                            return data.datasets[0].data[tooltipItem.index] + ' (' + Number.parseFloat(Math.abs(((value/total) * 100)).toFixed(2)).toString() + '%)';
                        }
                    }
                }
            }
        });
        getCardPartition(document.querySelector('form[name="{{ $name }}"]'), {{ $name }}, '{{ __('thousands_separator') }}', '{{ $chart['color'] }}');
    });
</script>
