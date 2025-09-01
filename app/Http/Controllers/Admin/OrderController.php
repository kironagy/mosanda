<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
     // عرض كل الأوردرات
    public function index()
    {
        $orders = Order::all()->map(function ($order) {
            $order->track_url = $order->address_in
                ? "https://api.paygate.to/control/track.php?address=" . $order->address_in
                : null;
            return $order;
        });

        return response()->json($orders);
    }

    // عرض تفاصيل أوردر واحد
    public function show(Order $order)
    {
        $order->track_url = $order->address_in
            ? "https://api.paygate.to/control/track.php?address=" . $order->address_in
            : null;

        return response()->json($order);
    }

  public function updateStatus(Order $order)
    {
        if (!$order->ipn_token) {
            return response()->json(["error" => "Order has no ipn_token"], 400);
        }

        $response = Http::get("https://api.paygate.to/control/payment-status.php", [
            "ipn_token" => $order->ipn_token
        ]);

        if ($response->failed()) {
            return response()->json(["error" => "Failed to fetch status from Paygate"], 500);
        }

        $data = $response->json();
        $status = $data["status"] ?? null;

        if ($status) {
            $order->status = $status;
            $order->save();
        }

        return response()->json([
            "order_id" => $order->id,
            "status"   => $order->status,
            "raw"      => $data
        ]);
    }

}
