<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Pakeges;
use App\Models\Order;

class PaymentController extends Controller
{
    public function pay($id)
    {
        $package = Pakeges::findOrFail($id);

        $client = new Client();
        $response = $client->post(env('COINGATE_API_URL') . '/orders', [
            'headers' => [
                'Authorization' => 'Token ' . env('COINGATE_API'),
                'Accept'        => 'application/json',
            ],
            'form_params' => [
                'order_id'       => uniqid(),
                'price_amount'   => $package->price,
                'price_currency' => 'EUR',
                'receive_currency' => 'BTC',
                'callback_url'   => route('coingate.callback'),
                'cancel_url'     => route('coingate.cancel'),
                'success_url'    => route('coingate.success'),
            ],
        ]);

        $order = json_decode($response->getBody(), true);

        // هنا ممكن تحفظ order في DB
        Order::create([
          'order_id'     => uniqid(), // أو أي رقم تعريف داخلي
            'pakeges_id'   => $package->id,
            'amount'       => $package->price,
            'status'       => $order['status'] ?? 'new',
            'coingate_id'  => $order['id'] ?? null,
        ]);

        return response()->json([
            'status'       => 'success',
            'payment_url'  => $order['payment_url'] ?? null,
            'coingate_id'  => $order['id'] ?? null,
        ]);
    }

    public function callback(Request $request)
    {
        // سجل بيانات الدفع من Coingate
        \Log::info('Coingate Callback', $request->all());

        // تقدر تحدث حالة الطلب في DB
        if ($request->has('id')) {
            Order::where('coingate_id', $request->id)
                ->update(['status' => $request->status ?? 'unknown']);
        }

        return response()->json(['message' => 'OK']);
    }

    public function success()
    {
        return response()->json(['message' => '✅ Payment successful']);
    }

    public function cancel()
    {
        return response()->json(['message' => '❌ Payment cancelled']);
    }
}
