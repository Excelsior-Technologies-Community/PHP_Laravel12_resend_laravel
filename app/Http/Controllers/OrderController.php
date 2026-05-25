<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\EmailLog;
use App\Mail\OrderReceiptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            ->latest()
            ->paginate(10);

        $statistics = $this->getEmailStatistics();

        return view('orders.index', compact('orders', 'statistics'));
    }


    public function sendReceipt($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Send email via Resend
            Mail::to(env('MAIL_TEST_EMAIL'))->send(new OrderReceiptMail($order));

            // Log successful email
            EmailLog::create([
                'order_id' => $order->id,
                'recipient_email' => $order->customer_email,
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return redirect()->back()->with('success', "✓ Receipt for {$order->order_no} sent successfully to {$order->customer_email}!");
        } catch (\Exception $e) {
            // Log failed email
            EmailLog::create([
                'order_id' => $order->id,
                'recipient_email' => $order->customer_email,
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', "✗ Failed to send receipt for {$order->order_no}: " . $e->getMessage());
        }
    }

    public function sendBatchReceipts(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id'
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $results = [];

        foreach ($request->order_ids as $orderId) {
            try {
                $order = Order::find($orderId);

                Mail::to(env('MAIL_TEST_EMAIL'))->send(new OrderReceiptMail($order));

                EmailLog::create([
                    'order_id' => $order->id,
                    'recipient_email' => $order->customer_email,
                    'status' => 'sent',
                    'sent_at' => now()
                ]);

                $sentCount++;
                $results[] = "✓ {$order->order_no} sent to {$order->customer_email}";
            } catch (\Exception $e) {
                EmailLog::create([
                    'order_id' => $order->id,
                    'recipient_email' => $order->customer_email,
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);

                $failedCount++;
                $results[] = "✗ {$order->order_no} failed: " . $e->getMessage();
                $failedOrders[] = $order->order_no;
            }
        }

        $message = "Batch sending completed! Sent: {$sentCount}, Failed: {$failedCount}";

        return redirect()->back()->with('batch_results', [
            'message' => $message,
            'details' => $results,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);
    }

    public function resendFailedEmail($id)
    {
        try {
            $emailLog = EmailLog::findOrFail($id);
            $order = $emailLog->order;

            Mail::to(env('MAIL_TEST_EMAIL'))->send(new OrderReceiptMail($order));

            // Update the existing log
            $emailLog->update([
                'status' => 'sent',
                'error_message' => null,
                'sent_at' => now()
            ]);

            return redirect()->back()->with('success', "✓ Successfully resent receipt to {$order->customer_email}");
        } catch (\Exception $e) {
            $emailLog->update([
                'error_message' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', "✗ Failed to resend: " . $e->getMessage());
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

                    $log->order->order_no,
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

    public function emailHistory()
    {
        $emailLogs = EmailLog::with('order')
            ->latest()
            ->paginate(20);

        $statistics = $this->getEmailStatistics();

        return view('orders.email-history', compact('emailLogs', 'statistics'));
    }

    private function getEmailStatistics()
    {
        return [
            'total_sent' => EmailLog::where('status', 'sent')->count(),
            'total_failed' => EmailLog::where('status', 'failed')->count(),
            'today_sent' => EmailLog::where('status', 'sent')
                ->whereDate('sent_at', today())
                ->count(),
            'unique_orders' => EmailLog::distinct('order_id')->count('order_id')
        ];
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

        return view('orders.email-report', compact('statistics', 'dailyStats', 'failedEmails'));
    }
}
