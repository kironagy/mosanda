<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Order;
use App\Models\Pakeges;
use Illuminate\Http\Request;

class DashboarController extends Controller
{
  public function getStatistics()
  {
    $statistics = [
      'total_requests' => Contacts::all()->count(),
      'pending_requests' => Contacts::where("status" , 'pending')->count(),
      'complete_requests' => Contacts::where("status" , 'completed')->count(),
      'total_orders' => Order::all()->count(),
    ];

    return response()->json($statistics);
  }

}
