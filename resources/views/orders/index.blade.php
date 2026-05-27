<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f4f6f9;
            transition: 0.3s;
        }

        .dark-mode {
            background: #121212 !important;
            color: white !important;
        }

        .dark-mode .card {
            background: #1e1e1e;
            color: white;
        }

        .dark-mode .table {
            color: white;
        }

        .dark-mode .table-dark {
            background: #000;
        }

        .card {
            border: none;
            overflow: hidden;
        }

        .stats-card {
            transition: .4s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <!-- Header -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2 class="fw-bold">
                <i class="fas fa-shopping-cart me-2"></i>
                Order Management Dashboard
            </h2>

            <div>

                <button onclick="toggleDarkMode()" class="btn btn-dark me-2">
                    🌙 Dark Mode
                </button>

                <a href="{{ route('orders.emailHistory') }}"
                    class="btn btn-outline-dark me-2">

                    <i class="fas fa-history"></i>
                    Email History

                </a>

                <a href="{{ route('orders.emailReport') }}"
                    class="btn btn-outline-info">

                    <i class="fas fa-chart-line"></i>
                    Reports

                </a>

            </div>

        </div>

        <!-- Statistics -->

        <div class="row mb-4">

            <div class="col-md-3 mb-3">
                <div class="card stats-card bg-success text-white shadow-sm rounded-4">
                    <div class="card-body">
                        <h6>Total Sent</h6>
                        <h2>{{ $statistics['total_sent'] }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card stats-card bg-danger text-white shadow-sm rounded-4">
                    <div class="card-body">
                        <h6>Failed</h6>
                        <h2>{{ $statistics['total_failed'] }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card stats-card bg-primary text-white shadow-sm rounded-4">
                    <div class="card-body">
                        <h6>Today Sent</h6>
                        <h2>{{ $statistics['today_sent'] }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card stats-card bg-dark text-white shadow-sm rounded-4">
                    <div class="card-body">
                        <h6>Unique Orders</h6>
                        <h2>{{ $statistics['unique_orders'] }}</h2>
                    </div>
                </div>
            </div>

        </div>

        <!-- Search -->

        <div class="card shadow-sm rounded-4 mb-4">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-8">

                        <form method="GET">

                            <div class="input-group">

                                <input type="text"
                                    class="form-control"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search order/customer/product">

                                <button class="btn btn-dark">
                                    <i class="fas fa-search"></i>
                                </button>

                                <a href="{{ url()->current() }}"
                                    class="btn btn-outline-secondary">

                                    Reset

                                </a>

                            </div>

                        </form>

                    </div>

                    <div class="col-md-4 text-end">

                        <a href="{{ route('orders.export') }}"
                            class="btn btn-success">

                            <i class="fas fa-download"></i>
                            Export CSV

                        </a>

                    </div>

                </div>

            </div>

        </div>

        <!-- Alerts -->

        @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

        @endif


        @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

        @endif

        <!-- Batch Form -->

        <form action="{{ route('orders.sendBatch') }}"
            method="POST"
            id="batchForm">

            @csrf

            <div class="card shadow-sm rounded-4 mb-4">

                <div class="card-header bg-dark text-white">

                    <h5 class="mb-0">
                        Batch Email Sending
                    </h5>

                </div>

                <div class="card-body">

                    <button type="submit"
                        class="btn btn-dark"
                        onclick="return confirmBatch()">

                        Send Selected Receipts

                    </button>

                </div>

            </div>

            <!-- Orders Table -->

            <div class="card shadow-sm rounded-4">

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover table-striped mb-0">

                            <thead class="table-dark">

                                <tr>

                                    <th width="50">

                                        <input type="checkbox"
                                            id="selectAll"
                                            class="form-check-input">

                                    </th>

                                    <th>Order No</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($orders as $order)

                                <tr>

                                    <td>

                                        <input type="checkbox"
                                            name="order_ids[]"
                                            value="{{ $order->id }}"
                                            class="order-checkbox form-check-input">

                                    </td>

                                    <td>
                                        <strong>{{ $order->order_no }}</strong>
                                    </td>

                                    <td>

                                        <div>
                                            {{ $order->customer_name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $order->customer_email }}
                                        </small>

                                    </td>

                                    <td>

                                        <span class="badge bg-info text-dark">
                                            {{ $order->product_name }}
                                        </span>

                                    </td>

                                    <td class="fw-bold text-success">

                                        ₹{{ number_format($order->price,2) }}

                                    </td>

                                    <td>

                                        @if($order->lastEmailLog)

                                            @if($order->lastEmailLog->status=="sent")

                                                <span class="badge bg-success">
                                                    Sent
                                                </span>

                                            @else

                                                <span class="badge bg-danger">
                                                    Failed
                                                </span>

                                            @endif

                                        @else

                                            <span class="badge bg-secondary">
                                                Not Sent
                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-end">

                                        <a href="{{ route('orders.sendReceipt',$order->id) }}"
                                            class="btn btn-dark btn-sm">

                                            Send

                                        </a>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="7"
                                        class="text-center py-5">

                                        No orders found.

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </form>

        <!-- Pagination -->

        <div class="mt-4">

            {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        document.getElementById('selectAll')
            ?.addEventListener('change', function() {

                document.querySelectorAll('.order-checkbox')
                    .forEach(box => {

                        box.checked = this.checked

                    });

            });

        function confirmBatch() {

            let selected =
                document.querySelectorAll('.order-checkbox:checked');

            if (selected.length === 0) {

                alert('Please select at least one order');

                return false;
            }

            return confirm(
                `Send receipts to ${selected.length} selected orders?`
            );
        }

        function toggleDarkMode() {

            document.body.classList.toggle('dark-mode');

            localStorage.setItem(
                'darkMode',
                document.body.classList.contains('dark-mode')
            );
        }

        window.onload = function() {

            if(localStorage.getItem('darkMode') === 'true') {

                document.body.classList.add('dark-mode');
            }
        }

    </script>

</body>

</html>