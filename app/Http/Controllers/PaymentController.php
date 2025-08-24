<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Package;
use App\Models\Order;
use App\Models\Pakeges;

class PaymentController extends Controller
{
    protected $client;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('NOWPAYMENTS_API_KEY');
        $this->apiUrl = env('NOWPAYMENTS_API_URL', 'https://api-sandbox.nowpayments.io/v1');
    }

    // إنشاء عملية شراء
    public function createPayment($pakageId)
    {
        $package = Pakeges::findOrFail($pakageId);
        $amount  = $package->price;

        $response = $this->client->post($this->apiUrl . '/payment', [
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'price_amount'      => $amount,
                'price_currency'    => 'usd',
                'pay_currency'      => 'btc',
                'ipn_callback_url'  => route('nowpayments.webhook'),
                'order_id'          => uniqid(),
                'order_description' => 'Package purchase #' . $package->id
            ]
        ]);

        $payment = json_decode($response->getBody(), true);

        if (!empty($payment['invoice_url'])) {
            Order::create([
                'pakeges_id' => $package->id,
                'order_id'   => $payment['order_id'],
                'amount'     => $amount,
                'status'     => 'pending',
                'payment_id' => $payment['payment_id'],
            ]);

            return response()->json([
                'invoice_url' => $payment['invoice_url'],
                'payment_id'  => $payment['payment_id']
            ]);
        }

        return response()->json(['error' => 'Could not create payment'], 500);
    }

    // Webhook من NowPayments
    public function webhook(Request $request)
    {
        $data = $request->all();

        // تحقق من التوقيع
        $ipnSecret = env('NOWPAYMENTS_IPN_SECRET');
        $hmac = hash_hmac("sha512", $request->getContent(), $ipnSecret);

        if ($request->header('x-nowpayments-sig') !== $hmac) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_id', $data['order_id'])->first();
        if ($order) {
            $order->status = $data['payment_status']; // pending, confirming, finished, failed
            $order->save();
        }

        return response()->json(['message' => 'ok']);
    }
}
