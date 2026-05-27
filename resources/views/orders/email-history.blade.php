<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Email History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body class="bg-light">

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2>
                <i class="fas fa-history"></i>
                Email Sending History
            </h2>

            <a href="{{ route('orders.index') }}"
                class="btn btn-dark">

                <i class="fas fa-arrow-left"></i>
                Back

            </a>

        </div>

        <!-- Filter -->

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form method="GET">

                    <div class="row">

                        <div class="col-md-4">

                            <select name="status"
                                class="form-select">

                                <option value="">
                                    All Status
                                </option>

                                <option value="sent"
                                    {{ request('status') == 'sent' ? 'selected' : '' }}>

                                    Sent

                                </option>

                                <option value="failed"
                                    {{ request('status') == 'failed' ? 'selected' : '' }}>

                                    Failed

                                </option>

                            </select>

                        </div>

                        <div class="col-md-2">

                            <button class="btn btn-dark w-100">
                                Filter
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <!-- Table -->

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
                                <th>Error</th>
                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($emailLogs as $log)

                            <tr>

                                <td>{{ $log->id }}</td>

                                <td>

                                    {{ $log->order->order_no ?? 'N/A' }}

                                </td>

                                <td>{{ $log->recipient_email }}</td>

                                <td>

                                    @if($log->status == 'sent')

                                    <span class="badge bg-success">
                                        Sent
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
                                        Failed
                                    </span>

                                    @endif

                                </td>

                                <td>

                                    {{ $log->sent_at ? $log->sent_at->format('d M Y h:i A') : '-' }}

                                </td>

                                <td class="text-danger">

                                    {{ $log->error_message ?? '-' }}

                                </td>

                                <td>

                                    @if($log->status == 'failed')

                                    <a href="{{ route('orders.resendFailed', $log->id) }}"
                                        class="btn btn-warning btn-sm">

                                        Resend

                                    </a>

                                    @endif

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center py-4">

                                    No email history found.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">

                    {{ $emailLogs->withQueryString()->links('pagination::bootstrap-5') }}

                </div>

            </div>

        </div>

    </div>

</body>

</html>