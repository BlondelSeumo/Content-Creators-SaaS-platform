<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin routes ( Needs to be placed above )
Route::group(['prefix' => 'admin', 'middleware' => 'jsVars'], function () {
    Voyager::routes();
    Route::get('/metrics/new/users/value', 'MetricsController@newUsersValue')->name('admin.metrics.new.users.value');
    Route::get('/metrics/new/users/trend', 'MetricsController@newUsersTrend')->name('admin.metrics.new.users.trend');
    Route::get('/metrics/new/users/partition', 'MetricsController@newUsersPartition')->name('admin.metrics.new.users.partition');
    Route::get('/metrics/subscriptions/value', 'MetricsController@subscriptionsValue')->name('admin.metrics.subscriptions.value');
    Route::get('/metrics/subscriptions/trend', 'MetricsController@subscriptionsTrend')->name('admin.metrics.subscriptions.trend');
    Route::get('/metrics/subscriptions/partition', 'MetricsController@subscriptionsPartition')->name('admin.metrics.subscriptions.partition');
});

// Home & contact page
Route::get('/', ['uses' => 'HomeController@index', 'as'   => 'home']);
Route::get('/contact', ['uses' => 'GenericController@contact', 'as'   => 'contact']);
Route::post('/contact/send', ['uses' => 'GenericController@sendContactMessage', 'as'   => 'contact.send']);

// Language switcher route
Route::get('language/{locale}', ['uses' => 'GenericController@setLanguage', 'as'   => 'language']);

/* Auth Routes + Verify password */
Auth::routes(['verify'=>true]);
Route::post('resendVerification', ['uses' => 'GenericController@resendConfirmationEmail', 'as'   => 'verfication.resend']);
// Social Auth login / register
Route::get('socialAuth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('socialAuth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
/*
 * (User) Protected routes
 */
Route::group(['middleware' => 'auth'], function () {
    // Settings panel routes
    Route::group(['prefix' => 'my', 'as' => 'my.'], function () {

        /*
         * (My) Settings
         */
        // Deposit - Payments
        Route::post('/settings/deposit/generateStripeSession', [
            'uses' => 'PaymentsController@generateStripeSession',
            'as'   => 'settings.deposit.generateStripeSession',
        ]);
        Route::post('/settings/flags/save', ['uses' => 'SettingsController@updateFlagSettings', 'as'   => 'settings.flags.save']);
        Route::post('/settings/profile/save', ['uses' => 'SettingsController@saveProfile', 'as'   => 'settings.profile.save']);
        Route::post('/settings/rates/save', ['uses' => 'SettingsController@saveRates', 'as'   => 'settings.rates.save']);
        Route::post('/settings/profile/upload/{uploadType}', ['uses' => 'SettingsController@uploadProfileAsset', 'as'   => 'settings.profile.upload']);
        Route::post('/settings/profile/remove/{assetType}', ['uses' => 'SettingsController@removeProfileAsset', 'as'   => 'settings.profile.remove']);
        Route::post('/settings/save', ['uses' => 'SettingsController@updateUserSettings', 'as'   => 'settings.save']);
        Route::post('/settings/verify/upload', ['uses' => 'SettingsController@verifyUpload', 'as'   => 'settings.verify.upload']);
        Route::post('/settings/verify/upload/delete', ['uses' => 'SettingsController@deleteVerifyAsset', 'as'   => 'settings.verify.delete']);
        Route::post('/settings/verify/save', ['uses' => 'SettingsController@saveVerifyRequest', 'as'   => 'settings.verify.save']);

        // Profile save
        Route::get('/settings/{type?}', ['uses' => 'SettingsController@index', 'as'   => 'settings']);
        Route::post('/settings/account/save', ['uses' => 'SettingsController@saveAccount', 'as'   => 'settings.account.save']);

        /*
         * (My) Notifications
         */
        Route::get('/notifications/{type?}', ['uses' => 'NotificationsController@index', 'as'   => 'notifications']);

        /*
         * (My) Messenger
         */
        Route::group(['prefix' => 'messenger', 'as' => 'messenger.'], function () {
            Route::get('/', ['uses' => 'MessengerController@index', 'as' => 'get']);
            Route::get('/fetchContacts', ['uses' => 'MessengerController@fetchContacts', 'as' => 'fetch']);
            Route::get('/fetchMessages/{userID}', 'MessengerController@fetchMessages', ['as' => 'fetch.user']);
            Route::post('/sendMessage', 'MessengerController@sendMessage', ['as' => 'send']);
            Route::post('/authorizeUser', 'MessengerController@authorizeUser', ['as' => 'authorize']);
            Route::post('/markSeen', 'MessengerController@markSeen', ['as' => 'mark']);
            Route::post('/getUserSearch', 'MessengerController@getUserSearch', ['as' => 'search']);
        });
        /*
         * (My) Bookmarks
         */
        Route::any('/bookmarks/{type?}', ['uses' => 'BookmarksController@index', 'as'   => 'bookmarks']);
//        Route::get('/bookmarks/{type}',['uses' => 'BookmarksController@filterBookmarks', 'as'   => 'bookmarks.filter']);

        /*
         * (My) Lists
         */
        Route::group(['prefix' => '', 'as' => 'lists.'], function () {
            Route::get('/lists', ['uses' => 'ListsController@index', 'as'   => 'all']);
            Route::post('/lists/save', ['uses' => 'ListsController@saveList', 'as'   => 'save']);
            Route::get('/lists/{list_id}', ['uses' => 'ListsController@showList', 'as'   => 'show']);
            Route::get('/lists/{list_id}', ['uses' => 'ListsController@showList', 'as'   => 'show']);
            Route::delete('/lists/delete', ['uses' => 'ListsController@deleteList', 'as'   => 'delete']);
            Route::post('/lists/members/save', ['uses' => 'ListsController@addListMember', 'as'   => 'members.save']);
            Route::delete('/lists/members/delete', ['uses' => 'ListsController@deleteListMember', 'as'   => 'members.delete']);
            Route::post('/lists/members/clear', ['uses' => 'ListsController@clearList', 'as'   => 'members.clear']);
            Route::post('/lists/manage/follows', ['uses' => 'ListsController@manageUserFollows', 'as'   => 'manage.follows']);
        });
    });

    Route::post('/report/content', ['uses' => 'ListsController@postReport', 'as'   => 'report.content']);

    Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
        Route::post('/initiate', ['uses' => 'PaymentsController@initiatePayment', 'as'   => 'initiatePayment']);
        Route::get('/paypal/status', ['uses' => 'PaymentsController@executePaypalPayment', 'as'   => 'executePaypalPayment']);
        Route::get('/stripe/status', ['uses' => 'PaymentsController@getStripePaymentStatus', 'as'   => 'checkStripePaymentStatus']);
        Route::get('/coinbase/status', ['uses' => 'PaymentsController@checkAndUpdateCoinbaseTransaction', 'as'   => 'checkCoinBasePaymentStatus']);
    });

    // Feed routes
    Route::get('/feed', ['uses' => 'FeedController@index', 'as'   => 'feed']);
    Route::get('/feed/posts', ['uses' => 'FeedController@getFeedPosts', 'as'   => 'feed.posts']);

    // File uploader routes
    Route::group(['prefix' => 'attachment', 'as' => 'attachment.'], function () {
        Route::post('/upload/{type}', ['uses' => 'AttachmentController@upload', 'as'   => 'upload']);
        Route::post('/remove', ['uses' => 'AttachmentController@removeAttachment', 'as'   => 'remove']);
    });

    // Posts routes
    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
        Route::post('/save', ['uses' => 'PostsController@savePost', 'as'   => 'save']);
        Route::get('/create', ['uses' => 'PostsController@create', 'as'   => 'create']);
        Route::get('/edit/{post_id}', ['uses' => 'PostsController@edit', 'as'   => 'edit']);
        Route::get('/{post_id}/{username}', ['uses' => 'PostsController@getPost', 'as'   => 'get']);
        Route::get('/comments', ['uses' => 'PostsController@getPostComments', 'as'   => 'get.comments']);
        Route::post('/comments/add', ['uses' => 'PostsController@addNewComment', 'as'   => 'add.comments']);
        Route::post('/reaction', ['uses' => 'PostsController@updateReaction', 'as'   => 'react']);
        Route::post('/bookmark', ['uses' => 'PostsController@updatePostBookmark', 'as'   => 'bookmark']);
        Route::delete('/delete', ['uses' => 'PostsController@deletePost', 'as'   => 'delete']);
    });

    // Subscriptions routes
    Route::group(['prefix' => 'subscriptions', 'as' => 'subscriptions.'], function () {
        Route::get('/{subscriptionId}/cancel', ['uses' => 'SubscriptionsController@cancelSubscription', 'as'   => 'cancel']);
    });

    // Withdrawals routes
    Route::group(['prefix' => 'withdrawals', 'as' => 'withdrawals.'], function () {
        Route::post('/request', ['uses' => 'WithdrawalsController@requestWithdrawal', 'as'   => 'request']);
    });

    // Invoices routes
    Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
        Route::get('/{id}', ['uses' => 'InvoicesController@index', 'as'   => 'get']);
    });

    // Countries routes
    Route::group(['prefix' => 'countries', 'as' => 'countries.'], function () {
        Route::get('', ['uses' => 'GenericController@countries', 'as'   => 'get']);
    });
});

Route::any('beacon/{type}', [
    'as'   => 'beacon.send',
    'uses' => 'StatsController@sendBeacon',
]);

Route::post('payment/stripeStatusUpdate', [
    'as'   => 'stripe.payment.update',
    'uses' => 'PaymentsController@stripePaymentsHook',
]);

Route::post('payment/paypalStatusUpdate', [
    'as'   => 'paypal.payment.update',
    'uses' => 'PaymentsController@paypalPaymentsHook',
]);

Route::post('payment/coinbaseStatusUpdate', [
    'as'   => 'coinbase.payment.update',
    'uses' => 'PaymentsController@coinbaseHook',
]);

// Install & upgrade routes
Route::get('/install', ['uses' => 'InstallerController@install', 'as'   => 'installer.install']);
Route::post('/install/savedbinfo', ['uses' => 'InstallerController@testAndSaveDBInfo', 'as'   => 'installer.savedb']);
Route::post('/install/beginInstall', ['uses' => 'InstallerController@beginInstall', 'as'   => 'installer.beginInstall']);
Route::get('/install/finishInstall', ['uses' => 'InstallerController@finishInstall', 'as'   => 'installer.finishInstall']);
Route::get('/update', ['uses' => 'InstallerController@upgrade', 'as'   => 'installer.update']);
Route::post('/update/doUpdate', ['uses' => 'InstallerController@doUpgrade', 'as'   => 'installer.doUpdate']);

// (Feed/Search) Suggestions filter
Route::post('/suggestions/members', ['uses' => 'FeedController@filterSuggestedMembers', 'as'   => 'suggestions.filter']);

// Public pages
Route::get('/pages/{slug}', ['uses' => 'PublicPagesController@getPage', 'as'   => 'pages.get']);

Route::get('/search', ['uses' => 'SearchController@index', 'as' => 'search.get']);
Route::get('/search/posts', ['uses' => 'SearchController@getSearchPosts', 'as' => 'search.posts']);
Route::get('/search/users', ['uses' => 'SearchController@getUsersSearch', 'as' => 'search.users']);

// Public profile
Route::get('/{username}', ['uses' => 'ProfileController@index', 'as'   => 'profile']);
Route::get('/{username}/posts', ['uses' => 'ProfileController@getUserPosts', 'as'   => 'profile.posts']);

Route::fallback(function () {
    return view('errors.404'); // template should exists
});
