<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2> Email Reports Dashboard</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-dark">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

      

        <!-- Daily Statistics -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"> Last 30 Days Activity</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sent</th>
                                <th>Failed</th>
                                <th>Total</th>
                                <th>Success Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyStats as $stat)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($stat->date)->format('M d, Y') }}</td>
                                <td><span class="badge bg-success">{{ $stat->sent_count }}</span></td>
                                <td><span class="badge bg-danger">{{ $stat->failed_count }}</span></td>
                                <td>{{ $stat->sent_count + $stat->failed_count }}</td>
                                <td>
                                    @php $rate = ($stat->sent_count + $stat->failed_count) > 0 ? round(($stat->sent_count / ($stat->sent_count + $stat->failed_count)) * 100) : 0; @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $rate }}%">
                                            {{ $rate }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Failed Emails List -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"> Recent Failed Emails</h5>
            </div>
            <div class="card-body">
                @if($failedEmails->count() > 0)
                    <div class="list-group">
                        @foreach($failedEmails as $failed)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $failed->order->order_no ?? 'N/A' }}</strong><br>
                                        To: {{ $failed->recipient_email }}<br>
                                        <small class="text-danger">{{ $failed->error_message }}</small>
                                    </div>
                                    <a href="{{ route('orders.resendFailed', $failed->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-redo"></i> Resend
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No failed emails found.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>