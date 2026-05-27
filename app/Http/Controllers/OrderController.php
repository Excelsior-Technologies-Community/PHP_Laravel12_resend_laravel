<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\EmailLog;
use App\Mail\OrderReceiptMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
  public function index(Request $request)
{
    $orders = Order::with('lastEmailLog')
        ->when($request->search, function ($query) use ($request) {

            $query->where('order_no', 'like', '%' . $request->search . '%')
                ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                ->orWhere('customer_email', 'like', '%' . $request->search . '%')
                ->orWhere('product_name', 'like', '%' . $request->search . '%');
        })
        ->oldest()
        ->paginate(3);

    // Statistics
    $statistics = [
        'total_sent' => \App\Models\EmailLog::where('status', 'sent')->count(),

        'total_failed' => \App\Models\EmailLog::where('status', 'failed')->count(),

        'today_sent' => \App\Models\EmailLog::whereDate('created_at', today())
                            ->where('status', 'sent')
                            ->count(),

        'unique_orders' => \App\Models\Order::count(),
    ];

    return view('orders.index', compact('orders', 'statistics'));
}

    public function sendReceipt($id)
    {
        try {

            $order = Order::findOrFail($id);

            Mail::to(env('MAIL_TEST_EMAIL'))
                ->queue(new OrderReceiptMail($order));

            EmailLog::create([
                'order_id' => $order->id,
                'recipient_email' => env('MAIL_TEST_EMAIL'),
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return redirect()->back()->with(
                'success',
                "✓ Receipt queued successfully for {$order->order_no}"
            );

        } catch (\Exception $e) {

            EmailLog::create([
                'order_id' => $id,
                'recipient_email' => env('MAIL_TEST_EMAIL'),
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return redirect()->back()->with(
                'error',
                "✗ Failed: " . $e->getMessage()
            );
        }
    }

    public function sendBatchReceipts(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id'
        ]);

        $sentCount = 0;

        foreach ($request->order_ids as $orderId) {

            $order = Order::find($orderId);

            Mail::to(env('MAIL_TEST_EMAIL'))
                ->queue(new OrderReceiptMail($order));

            EmailLog::create([
                'order_id' => $order->id,
                'recipient_email' => env('MAIL_TEST_EMAIL'),
                'status' => 'sent',
                'sent_at' => now()
            ]);

            $sentCount++;
        }

        return redirect()->back()->with(
            'success',
            "{$sentCount} emails queued successfully!"
        );
    }

    public function resendFailedEmail($id)
    {
        try {

            $emailLog = EmailLog::findOrFail($id);

            $order = $emailLog->order;

            Mail::to(env('MAIL_TEST_EMAIL'))
                ->queue(new OrderReceiptMail($order));

            $emailLog->update([
                'status' => 'sent',
                'error_message' => null,
                'sent_at' => now()
            ]);

            return redirect()->back()->with(
                'success',
                "✓ Email resent successfully!"
            );

        } catch (\Exception $e) {

            return redirect()->back()->with(
                'error',
                "✗ Failed: " . $e->getMessage()
            );
        }
    }

    public function export()
    {
        $logs = EmailLog::with('order')->get();

        $response = new StreamedResponse(function () use ($logs) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Order Number',
                'Recipient',
                'Status',
                'Date'
            ]);

            foreach ($logs as $log) {

                fputcsv($handle, [
                    $log->order->order_no ?? 'N/A',
                    $log->recipient_email,
                    $log->status,
                    $log->created_at
                ]);
            }

            fclose($handle);
        });

        $response->headers->set(
            'Content-Type',
            'text/csv'
        );

        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=email-report.csv'
        );

        return $response;
    }

    public function emailHistory(Request $request)
    {
        $emailLogs = EmailLog::with('order')
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(20);

        $statistics = $this->getEmailStatistics();

        return view('orders.email-history', compact(
            'emailLogs',
            'statistics'
        ));
    }

    public function emailReport()
    {
        $statistics = $this->getEmailStatistics();

        $dailyStats = EmailLog::select(
            DB::raw('DATE(sent_at) as date'),
            DB::raw('COUNT(case when status = "sent" then 1 end) as sent_count'),
            DB::raw('COUNT(case when status = "failed" then 1 end) as failed_count')
        )
            ->whereNotNull('sent_at')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $failedEmails = EmailLog::with('order')
            ->where('status', 'failed')
            ->latest()
            ->limit(10)
            ->get();

        return view('orders.email-report', compact(
            'statistics',
            'dailyStats',
            'failedEmails'
        ));
    }

    private function getEmailStatistics()
    {
        return [
            'total' => EmailLog::count(),
            'sent' => EmailLog::where('status', 'sent')->count(),
            'failed' => EmailLog::where('status', 'failed')->count(),
        ];
    }
}