<?php

namespace App\Services\WiPay;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WiPay Payment Gateway Service
 *
 * WiPay is a Caribbean payment processor based in Trinidad & Tobago.
 * Docs: https://wipayfinancial.com/developers
 *
 * Flow:
 * 1. Build a payment request → get a WiPay redirect URL
 * 2. Redirect user to WiPay checkout
 * 3. WiPay POSTs result to your return_url (callback)
 * 4. Verify and record the payment
 */
class WiPayService
{
    private string $env;

    private string $gatewayUrl;

    private string $accountNumber;

    private string $developerId;

    public function __construct()
    {
        $this->env           = config('wipay.env', 'sandbox');
        $this->accountNumber = $this->env === 'live'
            ? config('wipay.live_account_number')
            : config('wipay.account_number');
        $this->developerId = config('wipay.developer_id', '');
        $this->gatewayUrl  = $this->env === 'live'
            ? config('wipay.live_url')
            : config('wipay.sandbox_url');
    }

    /**
     * Build a WiPay payment request and return the redirect URL.
     *
     * @param  array  $params  {
     *    total: float,           // Amount in TTD (or configured currency)
     *    order_id: string,       // Your internal order/invoice reference
     *    name: string,           // Payer name
     *    email: string,          // Payer email
     *    phone: string,          // Payer phone
     *    description: string,    // Payment description
     * }
     * @return array{url: string, data: array}
     */
    public function buildPaymentRequest(array $params): array
    {
        $payload = [
            'account_number'  => $this->accountNumber,
            'avs'             => 0,
            'data'            => json_encode([
                'name'    => $params['name'],
                'email'   => $params['email'],
                'phone'   => $params['phone'] ?? '',
                'orderId' => $params['order_id'],
            ]),
            'environment'     => $this->env,
            'fee_structure'   => config('wipay.fee_structure', 'customer_pay'),
            'method'          => 'credit_card',
            'order_id'        => $params['order_id'],
            'return_url'      => url(config('wipay.return_url')),
            'total'           => number_format((float) $params['total'], 2, '.', ''),
            'currency'        => config('wipay.currency', 'TTD'),
            'country_code'    => config('wipay.country_code', 'TT'),
        ];

        if ($this->developerId) {
            $payload['developer_id'] = $this->developerId;
        }

        return [
            'url'  => $this->gatewayUrl,
            'data' => $payload,
        ];
    }

    /**
     * Handle the WiPay callback (POST from WiPay after payment).
     * Returns parsed result array.
     *
     * @param  array  $callbackData  Raw $_POST data from WiPay
     */
    public function handleCallback(array $callbackData): array
    {
        // WiPay sends: transaction_id, status, total, order_id, hash, etc.
        Log::info('WiPay callback received', ['data' => $callbackData]);

        $status = strtolower($callbackData['status'] ?? 'failed');

        return [
            'success'        => $status === 'successful',
            'status'         => $status,
            'transaction_id' => $callbackData['transaction_id'] ?? null,
            'order_id'       => $callbackData['order_id'] ?? null,
            'total'          => $callbackData['total'] ?? null,
            'raw'            => $callbackData,
        ];
    }

    public function isLive(): bool
    {
        return $this->env === 'live';
    }
}
