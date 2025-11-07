<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NinjaVan Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background:#f6f7fb; padding:20px; }
        .card { border-radius:8px; }
        .metric { font-size:1.8rem; font-weight:700; }
    </style>
</head>
<body>
<div class="container-fluid">
    <h1 class="mb-4 text-danger">📦 NinjaVan — Dashboard</h1>

    <!-- Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Total Parcels</div>
                <div class="metric">{{ number_format($totalParcel) }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Total Billing Weight</div>
                <div class="metric">{{ number_format($totalWeight,2) }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Average Weight</div>
                <div class="metric">{{ number_format($avgWeight,2) }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Delivered (approx)</div>
                <div class="metric">{{ number_format($delivered) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-3">
                <h5>Parcels by City (Top)</h5>
                <canvas id="cityChart" height="200"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-3">
                <h5>Parcel Size Distribution</h5>
                <canvas id="sizeChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-3">
                <h5>Top Customers (by parcel count)</h5>
                <canvas id="customerChart" height="200"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-3">
                <h5>Parcels per Day (Trend)</h5>
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <h5>Latest Parcels (preview)</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>To City</th>
                        <th>Parcel Size</th>
                        <th>Billing Weight</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // show latest 10 rows directly using DB
                        $latest = \Illuminate\Support\Facades\DB::table('asnp___ninja_van_at_pengkalan_chepa___dec_24')
    ->select(DB::raw('
        `Customer Name` as customer,
        `To Billing Zone` as city,
        `Parcel Size ID` as size,
        `Billing Weight` as weight,
        `Delivery Date` as ddate
    '))
    ->orderByDesc(DB::raw('`Delivery Date`'))
    ->limit(10)
    ->get();

                    @endphp

                    @foreach($latest as $r)
                        <tr>
                            <td>{{ $r->customer }}</td>
                            <td>{{ $r->city }}</td>
                            <td>{{ $r->size }}</td>
                            <td>{{ $r->weight }}</td>
                            <td>{{ $r->ddate }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    // Data passed from controller
    const cityLabels = {!! json_encode($cityLabels) !!};
    const cityData = {!! json_encode($cityData) !!};

    const sizeLabels = {!! json_encode($sizeLabels) !!};
    const sizeData = {!! json_encode($sizeData) !!};

    const customerLabels = {!! json_encode($customerLabels) !!};
    const customerData = {!! json_encode($customerData) !!};

    const trendLabels = {!! json_encode($trendLabels) !!};
    const trendData = {!! json_encode($trendData) !!};

    // City bar chart
    new Chart(document.getElementById('cityChart'), {
        type: 'bar',
        data: {
            labels: cityLabels,
            datasets: [{
                label: 'Parcels',
                data: cityData,
                borderWidth: 1,
                backgroundColor: 'rgba(230,0,18,0.7)'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // Size pie chart
    new Chart(document.getElementById('sizeChart'), {
        type: 'pie',
        data: {
            labels: sizeLabels,
            datasets: [{
                data: sizeData,
                backgroundColor: [
                    '#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'
                ]
            }]
        }
    });

    // Top customers horizontal bar
    new Chart(document.getElementById('customerChart'), {
        type: 'bar',
        data: {
            labels: customerLabels,
            datasets: [{
                label: 'Parcels',
                data: customerData,
                borderWidth: 1,
                backgroundColor: 'rgba(54,162,235,0.6)'
            }]
        },
        options: {
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
        }
    });

    // Trend line chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Parcels per Day',
                data: trendData,
                fill: true,
                tension: 0.2,
                backgroundColor: 'rgba(75,192,192,0.2)',
                borderColor: 'rgba(75,192,192,1)'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>

</body>
</html>
