<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Order Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">🛒 Order Management Dashboard</h2>
            <span class="badge bg-dark fs-6">Resend API Live</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                🎉 {{ session('success') }}
                <button type="button" class="btn-close" data-bootstrap-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Order No</th>
                            <th>Customer Name</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $order->order_no }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customer_name }}</div>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                </td>
                                <td><span class="badge bg-info text-dark">{{ $order->product_name }}</span></td>
                                <td class="fw-bold text-success">₹{{ number_format($order->price, 2) }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('orders.sendReceipt', $order->id) }}" class="btn btn-sm btn-dark rounded-2 px-3">
                                        Send Receipt ⚡
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>