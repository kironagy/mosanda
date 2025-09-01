<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PayService
{
    protected $merchantWallet = "0xdacb1d6b9d5181b84446d8a22f6f168636a4fc86";
    protected $currency = "USD";
    protected $provider = "rampnetwork";

    public function generatePayment($amount, $email, $pakegeId = null, $customType = null)
    {
        // 1. توليد payment_id
        $paymentId = round(microtime(true) * 1000) . "_" . rand(1000000, 9999999);

        // 2. تجهيز callback
        $callback = "https://your-app.com/paygate/webhook?payment=" . $paymentId;

        // 3. استدعاء API wallet.php
        $response = Http::get("https://api.paygate.to/control/wallet.php", [
            "address"  => $this->merchantWallet,
            "callback" => $callback,
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to connect to Paygate API");
        }

        $data = $response->json();
        $addressIn = $data["address_in"] ?? null;
        $ipnToken  = $data["ipn_token"] ?? null;

        if (!$addressIn) {
            throw new \Exception("No address_in found in API response");
        }

        // 4. بناء checkout link
        $checkoutLink = "https://checkout.paygate.to/process-payment.php"
            . "?address=" . $addressIn
            . "&amount=" . $amount
            . "&provider=" . $this->provider
            . "&email=" . urlencode($email ?? 'customer@example.com')
            . "&currency=" . $this->currency;

        // 5. حفظ الطلب في DB
        $order = Order::create([
            'pakeges_id' => $pakegeId,
            'amount'     => $amount,
            'status'     => 'new',
            'payment_id' => $paymentId,
            'address_in' => $addressIn,
            'ipn_token'  => $ipnToken,
        ]);

        return [
            "payment_id"   => $paymentId,
            "order_id"     => $order->id,
            "address_in"   => $addressIn,
            "checkout_url" => $checkoutLink,
        ];
    }
}
