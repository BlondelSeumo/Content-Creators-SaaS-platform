<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (! InstallerServiceProvider::checkIfInstalled()) {
            return false;
        }

        // Overriding config values for 3rd party implementations with DB values
        config(['laravel-ffmpeg.ffmpeg.binaries' => getSetting('media.ffmpeg_path', config('laravel-ffmpeg.ffmpeg.binaries'))]);
        config(['laravel-ffmpeg.ffprobe.binaries' => getSetting('media.ffprobe_path', config('laravel-ffmpeg.ffprobe.binaries'))]);

        if (getSetting('messenger-notifications.pusher_app_key')) {
            config(['broadcasting.connections.pusher.key' => getSetting('messenger-notifications.pusher_app_key')]);
        }

        if (getSetting('messenger-notifications.pusher_app_id')) {
            config(['broadcasting.connections.pusher.app_id' => getSetting('messenger-notifications.pusher_app_id')]);
        }

        if (getSetting('messenger-notifications.pusher_app_secret')) {
            config(['broadcasting.connections.pusher.secret' => getSetting('messenger-notifications.pusher_app_secret')]);
        }

        if (getSetting('messenger-notifications.pusher_app_cluster')) {
            config(['broadcasting.connections.pusher.options.cluster' => getSetting('messenger-notifications.pusher_app_cluster')]);
        }

        // if Pusher keys are set change default broadcasting driver
        if(self::hasPusherSettings()){
            config(['broadcasting.default' => 'pusher']);
        }

        config(['paypal.settings.mode' => getSetting('payments.paypal_live_mode') ? 'live' : 'sandbox']);

        if (getSetting('payments.paypal_client_id')) {
            config(['paypal.client_id' => getSetting('payments.paypal_client_id')]);
        }

        if (getSetting('payments.paypal_secret')) {
            config(['paypal.secret' => getSetting('payments.paypal_secret')]);
        }

        // Overriding default config values for logos & favicons, appending public path to them
        config(['app.site.light_logo' => asset(config('app.site.light_logo'))]);
        config(['app.site.dark_logo' => asset(config('app.site.dark_logo'))]);
        config(['app.site.favicon' => asset(config('app.site.favicon'))]);
        config(['app.admin.icon_image' => asset(config('app.admin.icon_image'))]);

        config(['mail.driver' => getSetting('emails.driver')]);
        config(['mail.from.name' => getSetting('emails.from_name')]);
        config(['mail.from.address' => getSetting('emails.from_address')]);

        config(['mail.host' => getSetting('emails.smtp_host')]);
        config(['mail.port' => getSetting('emails.smtp_port')]);
        config(['mail.encryption' => getSetting('emails.smtp_encryption')]);
        config(['mail.username' => getSetting('emails.smtp_username')]);
        config(['mail.password' => getSetting('emails.smtp_password')]);

        config(['services.mailgun.domain' => getSetting('emails.mailgun_domain')]);
        config(['services.mailgun.secret' => getSetting('emails.mailgun_secret')]);
        config(['services.mailgun.endpoint' => getSetting('emails.mailgun_endpoint')]);

        $storageDriver = getSetting('storage.driver') != null ? getSetting('storage.driver') : 'public';
        config(['filesystems.default' => $storageDriver]);
        config(['filesystems.defaultFilesystemDriver' => $storageDriver]);
        config(['voyager.storage.disk' => $storageDriver]);

        $awsRegion = getSetting('storage.aws_region') != null ? getSetting('storage.aws_region') : 'us-east-1';
        config(['cache.stores.dynamodb.key' => getSetting('storage.aws_access_key')]);
        config(['cache.stores.dynamodb.secret' => getSetting('storage.aws_secret_key')]);
        config(['cache.stores.dynamodb.region' => $awsRegion]);

        config(['filesystems.disks.s3.key' => getSetting('storage.aws_access_key')]);
        config(['filesystems.disks.s3.secret' => getSetting('storage.aws_secret_key')]);
        config(['filesystems.disks.s3.region' => $awsRegion]);
        config(['filesystems.disks.s3.bucket' => getSetting('storage.aws_bucket_name')]);

        config(['filesystems.disks.wasabi.key' => getSetting('storage.was_access_key')]);
        config(['filesystems.disks.wasabi.secret' => getSetting('storage.was_secret_key')]);
        config(['filesystems.disks.wasabi.region' => getSetting('storage.was_region')]);
        config(['filesystems.disks.wasabi.bucket' => getSetting('storage.was_bucket_name')]);

        config(['services.ses.key' => getSetting('storage.aws_access_key')]);
        config(['services.ses.secret' => getSetting('storage.aws_secret_key')]);
        config(['services.ses.s3.region' => $awsRegion]);

        config(['queue.connections.sqs.key' => getSetting('storage.aws_access_key')]);
        config(['queue.connections.sqs.secret' => getSetting('storage.aws_secret_key')]);
        config(['queue.connections.sqs.region' => $awsRegion]);

        if (getSetting('payments.currency_code') != null && ! empty(getSetting('payments.currency_code'))) {
            config(['app.site.currency_code' => getSetting('payments.currency_code')]);
        }

        if (getSetting('payments.currency_symbol') != null && ! empty(getSetting('payments.currency_symbol'))) {
            config(['app.site.currency_symbol' => getSetting('payments.currency_symbol')]);
        }

        config(['app.url' => getSetting('site.app_url')]);
        config(['filesystems.disks.public.url' =>  getSetting('site.app_url') . '/storage']);

        config(['laravelpwa.manifest.name' => getSetting('site.name')]);
        config(['laravelpwa.manifest.short_name' => getSetting('site.name')]);


        // PWA overrides
        config(['laravelpwa.manifest.icons.192x192.path' => asset(config('laravelpwa.manifest.icons.192x192.path'))]);
        config(['laravelpwa.manifest.icons.512x512.path' => asset(config('laravelpwa.manifest.icons.512x512.path'))]);

        config(['laravelpwa.manifest.splash.640x1136' => asset(config('laravelpwa.manifest.splash.640x1136'))]);
        config(['laravelpwa.manifest.splash.750x1334' => asset(config('laravelpwa.manifest.splash.750x1334'))]);
        config(['laravelpwa.manifest.splash.828x1792' => asset(config('laravelpwa.manifest.splash.828x1792'))]);
        config(['laravelpwa.manifest.splash.1125x2436' => asset(config('laravelpwa.manifest.splash.1125x2436'))]);
        config(['laravelpwa.manifest.splash.1242x2208' => asset(config('laravelpwa.manifest.splash.1242x2208'))]);
        config(['laravelpwa.manifest.splash.1242x2688' => asset(config('laravelpwa.manifest.splash.1242x2688'))]);
        config(['laravelpwa.manifest.splash.1536x2048' => asset(config('laravelpwa.manifest.splash.1536x2048'))]);
        config(['laravelpwa.manifest.splash.1668x2224' => asset(config('laravelpwa.manifest.splash.1668x2224'))]);
        config(['laravelpwa.manifest.splash.1668x2388' => asset(config('laravelpwa.manifest.splash.1668x2388'))]);
        config(['laravelpwa.manifest.splash.2048x2732' => asset(config('laravelpwa.manifest.splash.2048x2732'))]);

        // Social logins overrides
        if (getSetting('social-login.facebook_client_id')) {
            config(['services.facebook.client_id' => getSetting('social-login.facebook_client_id')]);
            config(['services.facebook.client_secret' => getSetting('social-login.facebook_secret')]);
            config(['services.facebook.redirect' => rtrim(getSetting('site.app_url'),'/').'/socialAuth/facebook/callback']);
        }
        if (getSetting('social-login.twitter_client_id')) {
            config(['services.twitter.client_id' => getSetting('social-login.twitter_client_id')]);
            config(['services.twitter.client_secret' => getSetting('social-login.twitter_secret')]);
            config(['services.twitter.redirect' => rtrim(getSetting('site.app_url'),'/').'/socialAuth/twitter/callback']);
        }
        if (getSetting('social-login.google_client_id')) {
            config(['services.google.client_id' => getSetting('social-login.google_client_id')]);
            config(['services.google.client_secret' => getSetting('social-login.google_secret')]);
            config(['services.google.redirect' => rtrim(getSetting('site.app_url'),'/').'/socialAuth/google/callback']);
        }

    }

    /**
     * Gets site's currency symbol with currency code fallback.
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public static function getWebsiteCurrencySymbol()
    {
        $symbol = '$';
        if (config('app.site.currency_symbol') != null && ! empty(config('app.site.currency_symbol'))) {
            $symbol = config('app.site.currency_symbol');
        } elseif (config('app.site.currency_code') != null && ! empty(config('app.site.currency_code'))) {
            $symbol = config('app.site.currency_code').' ';
        }

        return $symbol;
    }

    /**
     * Gets site's currency symbol.
     * @return bool|\Illuminate\Config\Repository|mixed
     */
    public static function getAppCurrencySymbol()
    {
        if (config('app.site.currency_symbol') != null && ! empty(config('app.site.currency_symbol'))) {
            return config('app.site.currency_symbol');
        }

        return false;
    }

    /**
     * Gets site's currency code.
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public static function getAppCurrencyCode()
    {
        $symbol = 'USD';
        if (config('app.site.currency_code') != null && ! empty(config('app.site.currency_code'))) {
            $symbol = config('app.site.currency_code');
        }

        return $symbol;
    }

    /**
     * Check if website has pusher settings set
     * @return bool
     */
    private static function hasPusherSettings(){
        return getSetting('messenger-notifications.pusher_app_cluster')
            && getSetting('messenger-notifications.pusher_app_key')
            && getSetting('messenger-notifications.pusher_app_secret')
            && getSetting('messenger-notifications.pusher_app_id');
    }
}
