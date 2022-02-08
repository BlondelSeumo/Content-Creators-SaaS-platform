<?php

return [
    // set your paypal credential
    'client_id' => '',
    'secret' => '',
    /*
     * SDK configuration
     */
    'settings' => [
        /*
         * Available option 'sandbox' or 'live'
         */
        'mode' => '',
        /*
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 30,
        /*
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
        /*
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path().'/logs/paypal.log',
        /*
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE',
    ],
];
