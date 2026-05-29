<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WiPay Payment Gateway Configuration
    | WiPay is a Caribbean payment processor (Trinidad & Tobago).
    | Docs: https://wipayfinancial.com/developers
    |--------------------------------------------------------------------------
    */

    'env' => env('WIPAY_ENV', 'sandbox'), // 'sandbox' or 'live'

    'account_number' => env('WIPAY_ACCOUNT_NUMBER', ''),

    'live_account_number' => env('LIVE_WIPAY_ACCOUNT_NUMBER', ''),

    'developer_id' => env('WIPAY_DEVELOPER_ID', ''),

    'fee_structure' => env('WIPAY_FEE_STRUCTURE', 'customer_pay'), // 'customer_pay' | 'merchant_pay' | 'split'

    'currency' => env('WIPAY_CURRENCY', 'TTD'),

    'country_code' => env('WIPAY_COUNTRY_CODE', 'TT'),

    'sandbox_url' => 'https://sandbox.wipayfinancial.com/plugins/payments',

    'live_url'    => 'https://wipayfinancial.com/plugins/payments',

    /*
    |--------------------------------------------------------------------------
    | Return / Callback Routes
    |--------------------------------------------------------------------------
    */
    'return_url'  => env('WIPAY_RETURN_URL', '/payments/callback'),
];
