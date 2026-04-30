<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NinjaVan Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background:#f6f7fb; padding:20px; }
        .card { border-radius:8px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .metric { font-size:1.8rem; font-weight:700; }
        h5 { font-weight: 600; color: #333; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <h1 class="mb-4 text-danger">📦 NinjaVan — Dashboard</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Total Parcels</div>
                <div class="metric">{{ number_format($totalParcel) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="text-muted">Total Weight</div>
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
                <h5>Top 3 States by Orders</h5>
                <canvas id="stateChart" height="200"></canvas>
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
                <h5>Parcels per Day (Trend)</h5>
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3">
                <h5>Customer Gender Distribution</h5>
                <div style="max-height: 300px; display: flex; justify-content: center;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <h5>Latest Parcels (preview)</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Gender (0=M, 1=F)</th>
                        <th>State</th>
                        <th>Parcel Size</th>
                        <th>Weight</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $latest = \Illuminate\Support\Facades\DB::table('ninjavan_data')
                            ->select(DB::raw('
                                `Gender` as gender,
                                `L1_Name` as state,
                                `Parcel_Size_ID` as size,
                                `Original_Weight` as weight,
                                `Delivery_Date` as ddate
                            '))
                            ->orderByDesc(DB::raw('`Delivery_Date`'))
                            ->limit(10)
                            ->get();
                    @endphp
                    @foreach($latest as $r)
                        <tr>
                            <td>{{ $r->gender }}</td>
                            <td>{{ $r->state }}</td>
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
    // 1. TOP 3 STATES CHART
    const stateLabels = {!! json_encode($stateLabels) !!};
    const stateData = {!! json_encode($stateData) !!};

    new Chart(document.getElementById('stateChart'), {
        type: 'bar',
        data: {
            labels: stateLabels,
            datasets: [{
                label: 'Parcels',
                data: stateData,
                backgroundColor: 'rgba(230,0,18,0.7)'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // 2. PARCEL SIZE CHART (FIXED GROUPING)
    const sizeRawKeys = {!! json_encode($sizeLabels) !!};
    const sizeRawValues = {!! json_encode($sizeData) !!};
    
    // Group everything into 'Small' (ID 1) or 'Other'
    let groupedSize = { 'Small': 0, 'Other': 0 };
    sizeRawKeys.forEach((id, index) => {
        if (id == 1) {
            groupedSize['Small'] += sizeRawValues[index];
        } else {
            groupedSize['Other'] += sizeRawValues[index];
        }
    });

    new Chart(document.getElementById('sizeChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(groupedSize),
            datasets: [{
                data: Object.values(groupedSize),
                backgroundColor: ['#4e73df', '#1cc88a']
            }]
        }
    });

    // 3. GENDER CHART (0=Male, 1=Female)
    const genderRaw = {!! json_encode($genderData) !!};
    const genderLabels = genderRaw.map(item => (item.Gender == 1 ? 'Female' : 'Male'));
    const genderCounts = genderRaw.map(item => item.count);

    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#36A2EB', '#FF6384']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 4. TREND CHART
    const trendLabels = {!! json_encode($trendLabels) !!};
    const trendData = {!! json_encode($trendData) !!};

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