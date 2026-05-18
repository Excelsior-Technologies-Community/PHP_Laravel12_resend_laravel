<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history"></i> Email Sending History</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-dark">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Order No</th>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Error Message</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emailLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->order->order_no ?? 'N/A' }}</td>
                                <td>{{ $log->recipient_email }}</td>
                                <td>
                                    @if($log->status == 'sent')
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Sent</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times"></i> Failed</span>
                                    @endif
                                </td>
                                <td>{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                                <td class="text-danger">{{ $log->error_message ?? '-' }}</td>
                                <td>
                                    @if($log->status == 'failed')
                                        <a href="{{ route('orders.resendFailed', $log->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-redo"></i> Resend
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No email logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $emailLogs->links() }}
            </div>
        </div>
    </div>
</body>
</html>