<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Order Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">
                 Order Management Dashboard
            </h2>
            <div>
                <a href="{{ route('orders.emailHistory') }}" class="btn btn-outline-dark me-2">
                     Email History
                </a>
                <a href="{{ route('orders.emailReport') }}" class="btn btn-outline-info">
                Reports
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
       

        <!-- Batch Send Form -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"> Batch Email Sending</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.sendBatch') }}" method="POST" id="batchForm">
                    @csrf
                    <div class="alert alert-info">
                        Select multiple orders to send receipts in batch
                    </div>
                    <button type="submit" class="btn btn-dark" onclick="return confirmBatch()">
                         Send Selected Receipts
                    </button>
                </form>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('batch_results'))
        <div class="alert alert-{{ session('batch_results.failed_count') > 0 ? 'warning' : 'success' }} alert-dismissible fade show">
            <strong> {{ session('batch_results.message') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <div class="mt-2 small">
                @foreach(session('batch_results.details') as $detail)
                    <div>{{ $detail }}</div>
                @endforeach
            </div>
        </div>
        @endif

       <!-- Orders Table -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">

        <table class="table table-hover table-striped mb-0 align-middle">

            <thead class="table-dark">
                <tr>
                    <th width="50">
                        <input type="checkbox"
                               id="selectAll"
                               class="form-check-input">
                    </th>

                    <th class="ps-4">Order No</th>
                    <th>Customer Name</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Email Status</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>

            <tbody>

            @forelse($orders as $order)

                <tr>

                    <!-- Checkbox -->
                    <td>
                        <input type="checkbox"
                               name="order_ids[]"
                               form="batchForm"
                               value="{{ $order->id }}"
                               class="order-checkbox form-check-input">
                    </td>

                    <!-- Order No -->
                    <td class="ps-4 fw-bold text-secondary">
                        {{ $order->order_no }}
                    </td>

                    <!-- Customer -->
                    <td>
                        <div class="fw-semibold">
                            {{ $order->customer_name }}
                        </div>

                        <small class="text-muted">
                            {{ $order->customer_email }}
                        </small>
                    </td>

                    <!-- Product -->
                    <td>
                        <span class="badge bg-info text-dark">
                            {{ $order->product_name }}
                        </span>
                    </td>

                    <!-- Price -->
                    <td class="fw-bold text-success">
                        ₹{{ number_format($order->price, 2) }}
                    </td>

                    <!-- Email Status -->
                    <td>

                        @if($order->lastEmailLog)

                            @if($order->lastEmailLog->status == 'sent')

                                <span class="badge bg-success">
                                    Sent

                                    {{ $order->lastEmailLog->sent_at
                                        ? $order->lastEmailLog->sent_at->diffForHumans()
                                        : '' }}
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Failed
                                </span>

                            @endif

                        @else

                            <span class="badge bg-secondary">
                                Not sent
                            </span>

                        @endif

                    </td>

                    <!-- Action -->
                    <td class="text-end pe-4">

                        <a href="{{ route('orders.sendReceipt', $order->id) }}"
                           class="btn btn-sm btn-dark rounded-2 px-3">

                            Send

                        </a>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7"
                        class="text-center py-4 text-muted">

                        No orders found.

                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function confirmBatch() {
            const selected = document.querySelectorAll('.order-checkbox:checked');
            if(selected.length === 0) {
                alert('Please select at least one order to send receipts.');
                return false;
            }
            return confirm(`Are you sure you want to send receipts to ${selected.length} selected orders?`);
        }
    </script>
</body>
</html>