<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\OrderReceiptMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function sendReceipt($id)
    {
        $order = Order::findOrFail($id);

        Mail::to('example@gmail.com')->send(new OrderReceiptMail($order));

        return redirect()->back()->with('success', "Receipt for {$order->order_no} sent successfully!");
    }
}