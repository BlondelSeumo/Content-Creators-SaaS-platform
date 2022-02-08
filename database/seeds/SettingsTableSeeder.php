<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('settings')->delete();
        \DB::table('settings')->insert(array (
            0 =>
            array (
                'id' => 1,
                'key' => 'site.name',
                'display_name' => 'Site Name',
                'value' => 'Justfans',
                'details' => '',
                'type' => 'text',
                'order' => 1,
                'group' => 'Site',
            ),
            1 =>
            array (
                'id' => 2,
                'key' => 'site.description',
                'display_name' => 'Site Description',
                'value' => 'Site Description',
                'details' => '',
                'type' => 'text',
                'order' => 4,
                'group' => 'Site',
            ),
            2 =>
            array (
                'id' => 4,
                'key' => 'site.google_analytics_tracking_id',
                'display_name' => 'Google Analytics Tracking ID',
                'value' => NULL,
                'details' => '',
                'type' => 'text',
                'order' => 34,
                'group' => 'Site',
            ),
            3 =>
            array (
                'id' => 5,
                'key' => 'admin.bg_image',
                'display_name' => 'Admin Background Image',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 5,
                'group' => 'Admin',
            ),
            4 =>
            array (
                'id' => 6,
                'key' => 'admin.title',
                'display_name' => 'Admin Title',
                'value' => 'JustFans  Admin',
                'details' => '',
                'type' => 'text',
                'order' => 1,
                'group' => 'Admin',
            ),
            5 =>
            array (
                'id' => 7,
                'key' => 'admin.description',
                'display_name' => 'Admin Description',
                'value' => 'Welcome to JustFans Admin Panel. Log in to manage and customize your site!',
                'details' => '',
                'type' => 'text',
                'order' => 2,
                'group' => 'Admin',
            ),
            6 =>
            array (
                'id' => 8,
                'key' => 'admin.loader',
                'display_name' => 'Admin Loader',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 3,
                'group' => 'Admin',
            ),
            7 =>
            array (
                'id' => 9,
                'key' => 'admin.icon_image',
                'display_name' => 'Admin Icon Image',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 4,
                'group' => 'Admin',
            ),
            8 =>
            array (
                'id' => 10,
                'key' => 'admin.google_analytics_client_id',
            'display_name' => 'Google Analytics Client ID (used for admin dashboard)',
                'value' => NULL,
                'details' => '',
                'type' => 'text',
                'order' => 1,
                'group' => 'Admin',
            ),
            9 =>
            array (
                'id' => 12,
                'key' => 'feed.feed_posts_per_page',
                'display_name' => 'Posts per page',
                'value' => '3',
                'details' => NULL,
                'type' => 'text',
                'order' => 6,
                'group' => 'Feed',
            ),
            10 =>
            array (
                'id' => 15,
                'key' => 'feed.feed_suggestions_card_per_page',
                'display_name' => 'Suggestion box cards per page',
                'value' => '3',
                'details' => NULL,
                'type' => 'text',
                'order' => 8,
                'group' => 'Feed',
            ),
            11 =>
            array (
                'id' => 16,
                'key' => 'feed.feed_suggestions_total_cards',
                'display_name' => 'Suggestion box total cards',
                'value' => '3',
                'details' => NULL,
                'type' => 'text',
                'order' => 7,
                'group' => 'Feed',
            ),
            12 =>
            array (
                'id' => 30,
                'key' => 'media.ffprobe_path',
                'display_name' => 'FFProbe Path',
                'value' => NULL,
                'details' => '',
                'type' => 'text',
                'order' => 13,
                'group' => 'Media',
            ),
            13 =>
            array (
                'id' => 31,
                'key' => 'media.ffmpeg_path',
                'display_name' => 'FFMpeg Path',
                'value' => NULL,
                'details' => '',
                'type' => 'text',
                'order' => 12,
                'group' => 'Media',
            ),
            14 =>
            array (
                'id' => 32,
                'key' => 'media.allowed_file_extensions',
                'display_name' => 'Allowed file extensions',
                'value' => 'png,jpg,jpeg,wav,mp3,ogg,mp4,avi,mov,moov,m4v,mpeg,wmv,avi',
                'details' => '{
"description": "If ffmpeg is not available, video formats will fallback to mp4 only."
}',
                'type' => 'text',
                'order' => 14,
                'group' => 'Media',
            ),
            15 =>
            array (
                'id' => 33,
                'key' => 'media.max_file_upload_size',
                'display_name' => 'Max file uploads size',
                'value' => '9',
                'details' => '{
"description": "File size in MB."
}',
                'type' => 'text',
                'order' => 15,
                'group' => 'Media',
            ),
            16 =>
            array (
                'id' => 34,
                'key' => 'messenger-notifications.messenger.pusher_app_key',
                'display_name' => 'Pusher App Key',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 16,
                'group' => 'Messenger & Notifications',
            ),
            17 =>
            array (
                'id' => 36,
                'key' => 'messenger-notifications.messenger.pusher_app_secret',
                'display_name' => 'Pusher App Secret',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 19,
                'group' => 'Messenger & Notifications',
            ),
            18 =>
            array (
                'id' => 37,
                'key' => 'messenger-notifications.messenger.pusher_app_cluster',
                'display_name' => 'Pusher Cluster Zone',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 20,
                'group' => 'Messenger & Notifications',
            ),
            19 =>
            array (
                'id' => 38,
                'key' => 'messenger-notifications.messenger.pusher_app_id',
                'display_name' => 'Pusher App ID',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 18,
                'group' => 'Messenger & Notifications',
            ),
            20 =>
            array (
                'id' => 39,
                'key' => 'invoices.sender_name',
                'display_name' => 'Invoices Sender Name',
                'value' => 'Web Development for Digital Marketing Agency',
                'details' => NULL,
                'type' => 'text',
                'order' => 21,
                'group' => 'Invoices',
            ),
            21 =>
            array (
                'id' => 40,
                'key' => 'invoices.sender_country_name',
                'display_name' => 'Invoices Sender Country Name',
                'value' => 'Australia',
                'details' => NULL,
                'type' => 'text',
                'order' => 22,
                'group' => 'Invoices',
            ),
            22 =>
            array (
                'id' => 41,
                'key' => 'invoices.sender_street_address',
                'display_name' => 'Invoices Sender Street Address',
                'value' => '121 Kopkes Road',
                'details' => NULL,
                'type' => 'text',
                'order' => 23,
                'group' => 'Invoices',
            ),
            23 =>
            array (
                'id' => 42,
                'key' => 'invoices.sender_state_name',
                'display_name' => 'Invoices Sender State Name',
                'value' => 'Victoria',
                'details' => NULL,
                'type' => 'text',
                'order' => 24,
                'group' => 'Invoices',
            ),
            24 =>
            array (
                'id' => 43,
                'key' => 'invoices.sender_city_name',
                'display_name' => 'Invoices Sender City Name',
                'value' => '3351',
                'details' => NULL,
                'type' => 'text',
                'order' => 25,
                'group' => 'Invoices',
            ),
            25 =>
            array (
                'id' => 44,
                'key' => 'invoices.sender_postcode',
                'display_name' => 'Invoices Sender Postcode',
                'value' => 'PITFIELD',
                'details' => NULL,
                'type' => 'text',
                'order' => 26,
                'group' => 'Invoices',
            ),
            26 =>
            array (
                'id' => 45,
                'key' => 'invoices.sender_company_number',
                'display_name' => 'Invoices Sender Company Number',
            'value' => '(03) 5391 1216',
                'details' => NULL,
                'type' => 'text',
                'order' => 27,
                'group' => 'Invoices',
            ),
            27 =>
            array (
                'id' => 46,
                'key' => 'invoices.prefix',
                'display_name' => 'Invoices Prefix',
                'value' => 'OF',
                'details' => NULL,
                'type' => 'text',
                'order' => 28,
                'group' => 'Invoices',
            ),
            28 =>
            array (
                'id' => 52,
                'key' => 'media.apply_watermark',
                'display_name' => 'Apply watermark',
                'value' => '0',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true
}',
                'type' => 'checkbox',
                'order' => 32,
                'group' => 'Media',
            ),
            29 =>
            array (
                'id' => 53,
                'key' => 'media.watermark_image',
                'display_name' => 'Watermark image',
                'value' => '',
                'details' => NULL,
                'type' => 'file',
                'order' => 33,
                'group' => 'Media',
            ),
            30 =>
            array (
                'id' => 54,
                'key' => 'site.light_logo',
                'display_name' => 'Light site logo',
                'value' => '',
                'details' => NULL,
                'type' => 'file',
                'order' => 35,
                'group' => 'Site',
            ),
            31 =>
            array (
                'id' => 55,
                'key' => 'site.dark_logo',
                'display_name' => 'Dark site logo',
                'value' => '',
                'details' => NULL,
                'type' => 'file',
                'order' => 36,
                'group' => 'Site',
            ),
            32 =>
            array (
                'id' => 56,
                'key' => 'site.favicon',
                'display_name' => 'Site favicon',
                'value' => '',
                'details' => NULL,
                'type' => 'file',
                'order' => 65,
                'group' => 'Site',
            ),
            33 =>
            array (
                'id' => 57,
                'key' => 'payments.stripe_public_key',
                'display_name' => 'Stripe Public Key',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 37,
                'group' => 'Payments',
            ),
            34 =>
            array (
                'id' => 58,
                'key' => 'payments.stripe_secret_key',
                'display_name' => 'Stripe Secret Key',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 38,
                'group' => 'Payments',
            ),
            35 =>
            array (
                'id' => 59,
                'key' => 'payments.stripe_webhooks_secret',
                'display_name' => 'Stripe Webhooks Secret',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 39,
                'group' => 'Payments',
            ),
            36 =>
            array (
                'id' => 60,
                'key' => 'payments.paypal_client_id',
                'display_name' => 'Paypal Client Id',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 40,
                'group' => 'Payments',
            ),
            37 =>
            array (
                'id' => 61,
                'key' => 'payments.paypal_secret',
                'display_name' => 'Paypal Secret',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 41,
                'group' => 'Payments',
            ),
            38 =>
            array (
                'id' => 74,
                'key' => 'payments.paypal_live_mode',
                'display_name' => 'Paypal Live Mode',
                'value' => '0',
                'details' => '{
"true" : "On",
"false" : "Off",
"checked" : true
}',
                'type' => 'checkbox',
                'order' => 42,
                'group' => 'Payments',
            ),
            39 =>
            array (
                'id' => 78,
                'key' => 'emails.driver',
                'display_name' => 'Email driver',
                'value' => 'log',
                'details' => '{
"default" : "log",
"options" : {
"log": "Log",
"smtp": "SMTP",
"mailgun": "Mailgun"
}
}',
                'type' => 'select_dropdown',
                'order' => 43,
                'group' => 'Emails',
            ),
            40 =>
            array (
                'id' => 79,
                'key' => 'emails.from_name',
                'display_name' => 'Mail from name',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 44,
                'group' => 'Emails',
            ),
            41 =>
            array (
                'id' => 80,
                'key' => 'emails.from_address',
                'display_name' => 'Mail from address',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 45,
                'group' => 'Emails',
            ),
            42 =>
            array (
                'id' => 81,
                'key' => 'emails.mailgun_domain',
                'display_name' => 'Mailgun domain',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 46,
                'group' => 'Emails',
            ),
            43 =>
            array (
                'id' => 82,
                'key' => 'emails.mailgun_secret',
                'display_name' => 'Mailgun secret',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 47,
                'group' => 'Emails',
            ),
            44 =>
            array (
                'id' => 83,
                'key' => 'emails.smtp_host',
                'display_name' => 'SMTP Host',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 49,
                'group' => 'Emails',
            ),
            45 =>
            array (
                'id' => 84,
                'key' => 'emails.smtp_port',
                'display_name' => 'SMTP Port',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 50,
                'group' => 'Emails',
            ),
            46 =>
            array (
                'id' => 85,
                'key' => 'emails.smtp_encryption',
                'display_name' => 'SMTP Encryption',
                'value' => 'tsl',
                'details' => '{
"default" : "tsl",
"options" : {
"tsl": "TSL",
"ssl": "SSL"
}
}',
                'type' => 'radio_btn',
                'order' => 51,
                'group' => 'Emails',
            ),
            47 =>
            array (
                'id' => 86,
                'key' => 'emails.smtp_username',
                'display_name' => 'SMTP Username',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 52,
                'group' => 'Emails',
            ),
            48 =>
            array (
                'id' => 87,
                'key' => 'emails.smtp_password',
                'display_name' => 'SMTP Password',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 53,
                'group' => 'Emails',
            ),
            49 =>
            array (
                'id' => 88,
                'key' => 'emails.mailgun_endpoint',
                'display_name' => 'Mailgun endpoint',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 48,
                'group' => 'Emails',
            ),
            50 =>
            array (
                'id' => 95,
                'key' => 'storage.driver',
                'display_name' => 'Driver',
                'value' => 'public',
                'details' => '{
"default" : "public",
"options" : {
"public": "Local",
"s3": "S3"
}
}',
                'type' => 'select_dropdown',
                'order' => 54,
                'group' => 'Storage',
            ),
            51 =>
            array (
                'id' => 96,
                'key' => 'storage.aws_access_key',
                'display_name' => 'Aws Access Key',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 55,
                'group' => 'Storage',
            ),
            52 =>
            array (
                'id' => 97,
                'key' => 'storage.aws_secret_key',
                'display_name' => 'Aws Secret Key',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 56,
                'group' => 'Storage',
            ),
            53 =>
            array (
                'id' => 98,
                'key' => 'storage.aws_region',
                'display_name' => 'Aws Region',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 57,
                'group' => 'Storage',
            ),
            54 =>
            array (
                'id' => 99,
                'key' => 'storage.aws_bucket_name',
                'display_name' => 'Aws Bucket Name',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 58,
                'group' => 'Storage',
            ),
            55 =>
            array (
                'id' => 100,
                'key' => 'storage.aws_cdn_enabled',
                'display_name' => 'Aws CloudFront Enabled',
                'value' => '0',
                'details' => '{
"true" : "On",
"off" : "Off",
"checked" : false
}',
                'type' => 'checkbox',
                'order' => 59,
                'group' => 'Storage',
            ),
            56 =>
            array (
                'id' => 101,
                'key' => 'storage.aws_cdn_presigned_urls_enabled',
                'display_name' => 'Aws CloudFront PreSigned Url\'s Enabled',
                'value' => '0',
                'details' => '{
"true" : "On",
"false" : "Off",
"checked" : false
}',
                'type' => 'checkbox',
                'order' => 61,
                'group' => 'Storage',
            ),
            57 =>
            array (
                'id' => 102,
                'key' => 'storage.aws_cdn_key_pair_id',
                'display_name' => 'Aws CloundFront Key Paird Id',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 62,
                'group' => 'Storage',
            ),
            58 =>
            array (
                'id' => 103,
                'key' => 'storage.aws_cdn_private_key_path',
                'display_name' => 'Aws CloudFront Private Key Path',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 63,
                'group' => 'Storage',
            ),
            59 =>
            array (
                'id' => 104,
                'key' => 'storage.cdn_domain_name',
                'display_name' => 'Aws CloudFront Domain Name',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 60,
                'group' => 'Storage',
            ),
            60 =>
            array (
                'id' => 106,
                'key' => 'site.enable_cookies_box',
                'display_name' => 'Enable cookies box',
                'value' => '1',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true
}',
                'type' => 'checkbox',
                'order' => 68,
                'group' => 'Site',
            ),
            61 =>
            array (
                'id' => 108,
                'key' => 'site.allow_theme_switch',
                'display_name' => 'Allow theme switch',
                'value' => '1',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description" : "Allow users to switch between light and dark modes."
}',
                'type' => 'checkbox',
                'order' => 69,
                'group' => 'Site',
            ),
            62 =>
            array (
                'id' => 109,
                'key' => 'site.default_user_theme',
                'display_name' => 'Default theme',
                'value' => 'light',
                'details' => '{
"default" : "light",
"options" : {
"light": "Light theme",
"dark": "Dark theme"
}
}',
                'type' => 'radio_btn',
                'order' => 70,
                'group' => 'Site',
            ),
            63 =>
            array (
                'id' => 110,
                'key' => 'site.allow_direction_switch',
                'display_name' => 'Allow direction switch',
                'value' => '1',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description": "Allow users to switch site direction from ltr to rtl."
}',
                'type' => 'checkbox',
                'order' => 71,
                'group' => 'Site',
            ),
            64 =>
            array (
                'id' => 111,
                'key' => 'site.default_site_direction',
                'display_name' => 'Default site direction',
                'value' => 'ltr',
                'details' => '{
"default" : "ltr",
"options" : {
"ltr": "Left to right",
"rtl": "Right to left"
}
}',
                'type' => 'radio_btn',
                'order' => 73,
                'group' => 'Site',
            ),
            65 =>
            array (
                'id' => 112,
                'key' => 'site.allow_language_switch',
                'display_name' => 'Allow language switch',
                'value' => '1',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description": "Allow users to change site\'s language."
}',
                'type' => 'checkbox',
                'order' => 74,
                'group' => 'Site',
            ),
            66 =>
            array (
                'id' => 113,
                'key' => 'site.default_site_language',
                'display_name' => 'Default site language',
                'value' => 'en',
                'details' => '{
"description" : "Language code. Must have a present language file in the resources/lang directory."
}',
                'type' => 'text',
                'order' => 75,
                'group' => 'Site',
            ),
            67 =>
            array (
                'id' => 114,
                'key' => 'feed.disable_right_click',
                'display_name' => 'Disable right click on media & view page source',
                'value' => '0',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true
}',
                'type' => 'checkbox',
                'order' => 72,
                'group' => 'Feed',
            ),
            68 =>
            array (
                'id' => 116,
                'key' => 'site.homepage_type',
                'display_name' => 'Homepage type',
                'value' => 'landing',
                'details' => '{
"default" : "landing",
"options" : {
"landing": "Landing page",
"login": "Login page"
}
}',
                'type' => 'radio_btn',
                'order' => 76,
                'group' => 'Site',
            ),
            69 =>
            array (
                'id' => 119,
                'key' => 'site.enforce_user_identity_checks',
                'display_name' => 'Enforce User Identity Check',
                'value' => '0',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : true,
"description" : "If enabled, users will only be able to post content if verified."
}',
                'type' => 'checkbox',
                'order' => 78,
                'group' => 'Site',
            ),
            70 =>
            array (
                'id' => 120,
                'key' => 'site.currency_code',
                'display_name' => 'Site Currency Code',
                'value' => 'USD',
                'details' => NULL,
                'type' => 'text',
                'order' => 66,
                'group' => 'Site',
            ),
            71 =>
            array (
                'id' => 121,
                'key' => 'site.currency_symbol',
                'display_name' => 'Site Currency Symbol',
                'value' => '$',
                'details' => NULL,
                'type' => 'text',
                'order' => 67,
                'group' => 'Site',
            ),
            72 =>
            array (
                'id' => 123,
                'key' => 'site.app_url',
                'display_name' => 'Site url',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 2,
                'group' => 'Site',
            ),
            73 =>
            array (
                'id' => 124,
                'key' => 'site.allow_pwa_installs',
                'display_name' => 'Allow PWA Installs',
                'value' => '0',
                'details' => '{
"on" : "On"
"off" : "Off",
"checked" : false,
\'description\' : \'Turns the site into an installable PWA on all devices. Website must be server from a root domain.\'
}',
                'type' => 'checkbox',
                'order' => 79,
                'group' => 'Site',
            ),
            74 =>
            array (
                'id' => 126,
                'key' => 'social-media.facebook_url',
                'display_name' => 'Facebook',
                'value' => '#',
                'details' => NULL,
                'type' => 'text',
                'order' => 80,
                'group' => 'Social media',
            ),
            75 =>
            array (
                'id' => 127,
                'key' => 'social-media.instagram_url',
                'display_name' => 'Instagram',
                'value' => '#',
                'details' => NULL,
                'type' => 'text',
                'order' => 81,
                'group' => 'Social media',
            ),
            76 =>
            array (
                'id' => 128,
                'key' => 'social-media.twitter_url',
                'display_name' => 'Twitter',
                'value' => '#',
                'details' => NULL,
                'type' => 'text',
                'order' => 82,
                'group' => 'Social media',
            ),
            77 =>
            array (
                'id' => 129,
                'key' => 'social-media.whatsapp_url',
                'display_name' => 'Whatsapp',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 83,
                'group' => 'Social media',
            ),
            78 =>
            array (
                'id' => 130,
                'key' => 'social-media.tiktok_url',
                'display_name' => 'Tik Tok',
                'value' => '#',
                'details' => NULL,
                'type' => 'text',
                'order' => 84,
                'group' => 'Social media',
            ),
            79 =>
            array (
                'id' => 131,
                'key' => 'social-media.youtube_url',
                'display_name' => 'Youtube',
                'value' => NULL,
                'details' => NULL,
                'type' => 'text',
                'order' => 85,
                'group' => 'Social media',
            ),
            80 =>
            array (
                'id' => 138,
                'key' => 'withdrawals-deposit.withdrawal_min_amount',
                'display_name' => 'Withdrawal request minimum amount',
                'value' => '20',
                'details' => '{
"description": "Default: 20"
}',
                'type' => 'text',
                'order' => 91,
                'group' => 'Withdrawals & Deposit',
            ),
            81 =>
            array (
                'id' => 139,
                'key' => 'withdrawals-deposit.withdrawal_max_amount',
                'display_name' => 'Withdrawal request maximum amount',
                'value' => '500',
                'details' => '{
"description": "Default: 500"
}',
                'type' => 'text',
                'order' => 92,
                'group' => 'Withdrawals & Deposit',
            ),
            82 =>
            array (
                'id' => 140,
                'key' => 'withdrawals-deposit.deposit_min_amount',
                'display_name' => 'Deposit minimum amount',
                'value' => '5',
                'details' => '{
"description": "Default: 5"
}',
                'type' => 'text',
                'order' => 93,
                'group' => 'Withdrawals & Deposit',
            ),
            83 =>
            array (
                'id' => 141,
                'key' => 'withdrawals-deposit.deposit_max_amount',
                'display_name' => 'Deposit maximum amount',
                'value' => '500',
                'details' => '{
"description": "Default: 500"
}',
                'type' => 'text',
                'order' => 94,
                'group' => 'Withdrawals & Deposit',
            ),
        ));


    }
}
