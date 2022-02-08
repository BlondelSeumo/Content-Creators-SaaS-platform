<div class="col">
    <div class="metrics-container">

        <h3 class="d-flex align-items-center mb-4"><div class="icon voyager-dashboard"></div>{{__("Platform statistics")}}</h3>

        <div class="row">
            <div class="mb-4 col-md-4">
                <div class="card shadow rounded p-5">
                    <div class="card-body text-muted font-weight-medium">
                        <p class="font-weight-bolder">Last 24 hours</p>
                        <p class="">{{__("Users registered")}}: {{\App\Providers\DashboardServiceProvider::getLast24HoursRegisteredUsersCount()}}</p>
                        <p class="">{{__("New posts")}}: {{\App\Providers\DashboardServiceProvider::getLast24HoursPostsCount()}}</p>
                        <p class="">{{__("New subscriptions")}}: {{\App\Providers\DashboardServiceProvider::getLast24HoursSubscriptionsCount()}}</p>
                        <p class="m-0">{{__("Total earned")}}: {{\App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol()}}{{\App\Providers\DashboardServiceProvider::getLast24HoursTotalEarned()}}</p>
                        <span class="pull-right"><a href="admin/users" class="primary-link">{{__("Go to users")}} ››</a></span>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-4">

                <div class="card shadow rounded p-5">
                    <div class="card-body text-muted font-weight-medium">
                        <p class="font-weight-bolder">Payments</p>
                        <p>{{__("Active subscriptions")}}: {{\App\Providers\DashboardServiceProvider::getActiveSubscriptionsCount()}}</p>
                        <p>{{__("Subscriptions revenue")}}: {{\App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol()}}{{\App\Providers\DashboardServiceProvider::getTotalSubscriptionsRevenue()}}</p>
                        <p>{{__("Total transactions")}}: {{\App\Providers\DashboardServiceProvider::getTotalTransactionsCount()}}</p>
                        <p class="m-0">{{__("Total amount earned")}}: {{\App\Providers\SettingsServiceProvider::getWebsiteCurrencySymbol()}}{{\App\Providers\DashboardServiceProvider::getTotalEarned()}}</p>
                        <span class="pull-right"><a href="admin/transactions" class="primary-link">{{__("Go to payments")}} ››</a></span>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-4">

                <div class="card shadow rounded p-5">
                    <div class="card-body text-muted font-weight-medium">
                        <p class="font-weight-bolder">Content</p>
                        <p>{{__("Total posts")}}: {{\App\Providers\DashboardServiceProvider::getPostsCount()}}</p>
                        <p>{{__("Post attachments")}}: {{\App\Providers\DashboardServiceProvider::getPostAttachmentsCount()}}</p>
                        <p>{{__("Post comments")}}: {{\App\Providers\DashboardServiceProvider::getPostCommentsCount()}}</p>
                        <p class="m-0">{{__("Total reactions")}}: {{\App\Providers\DashboardServiceProvider::getReactionsCount()}}</p>
                        <span class="pull-right"><a href="admin/posts" class="primary-link">{{__("Go to content")}} ››</a></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row two-columns-graph-holder">
            @include('elements.admin.value_card', [
                'name' => 'newUsersValue',
                'route' => 'admin.metrics.new.users.value',
                'size' => 'col-xs-12 col-sm-12 col-md-6 col-lg-6',
                'title' => __('New users'),
                'form' => [
                    'trans' => [__('Day'), __('Days')],
                    'function' => 'count',
                    'ranges' => [1, 7, 14, 30, 60, 90],
                    'range' => 30
                ]
            ])
            @include('elements.admin.partition_card', [
                'name' => 'rolesPerUser',
                'chart' => [
                    'size' => 180,
                    'color' => '203, 12, 159',
                    'total' => true
                ],
                'route' => 'admin.metrics.new.users.partition',
                'size' => 'col-xs-12 col-sm-12 col-md-6 col-lg-6',
                'title' => __('Users roles'),
                'form' => [
                    'function' => 'count',
                ]
            ])
        </div>
        <div class="row">
            @include('elements.admin.trend_card', [
                'name' => 'newUsersTrend',
                'chart' => [
                    'size' => 100,
                    'color_start' => 'rgba(255, 105, 220)',
                    'color_stop' => 'rgba(207, 60, 172, 0.5)',
                    'border_color' => 'rgba(203, 12, 159, 0.7)',
                    'point_radius' => 10,
                    'total' => true
                ],
                'route' => 'admin.metrics.new.users.trend',
                'size' => 'col-12 col-lg-12 mb-4',
                'title' => __('Registered users'),
                'form' => [
                    'trans' => [__('Month'), __('Months')],
                    'function' => 'count',
                    'unit' => 'month',
                    'ranges' => [3, 6, 12],
                    'range' => 12
                ]
            ])
        </div>
    </div>
</div>
