@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Main Dashboard</h2>
            <p class="text-muted small mb-0">General Delivery Analytics</p>
        </div>
        
        <div class="d-flex gap-3 align-items-end">
            <form action="{{ url()->current() }}" method="GET" class="d-flex gap-3 align-items-end">
                <div>
                    <label class="filter-label d-block text-muted small fw-bold">SELECT YEAR</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="2023" {{ $selectedYear == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ $selectedYear == '2024' ? 'selected' : '' }}>2024</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label d-block text-muted small fw-bold">SELECT MONTH</label>
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>All Months</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ date("F", mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL PARCELS</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalParcel) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalWeight,2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">AVERAGE WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($avgWeight,2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">DELIVERED (APPROX)</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($delivered) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Top 3 States by Orders</h5>
                <canvas id="stateChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Parcel Size Distribution</h5>
                <canvas id="sizeChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Parcels per Day (Trend)</h5>
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0 text-center">
                <h5 class="fw-bold mb-3">Customer Gender Distribution</h5>
                <div style="max-height: 250px; display: flex; justify-content: center;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3 shadow-sm border-0">
        <h5 class="fw-bold mb-3">Latest Parcels ({{ $selectedYear }} {{ $selectedMonth !== 'all' ? '- Month '.$selectedMonth : '' }})</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Gender</th>
                        <th>State</th>
                        <th>Parcel Size</th>
                        <th>Weight</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $latest = \Illuminate\Support\Facades\DB::table('ninjavan_data')
                            ->where('Delivery_Date', 'LIKE', '%'.$selectedYear.'%');
                        
                        if($selectedMonth !== 'all') {
                            $formattedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
                            $latest->where('Delivery_Date', 'LIKE', '%/'.$formattedMonth.'/%');
                        }

                        $rows = $latest->orderByDesc('Delivery_Date')->limit(10)->get();
                    @endphp
                    @foreach($rows as $r)
                        <tr>
                            <td>{{ $r->Gender == 1 ? 'Female' : 'Male' }}</td>
                            <td>{{ $r->L1_Name }}</td>
                            <td>{{ $r->Parcel_Size_ID }}</td>
                            <td>{{ $r->Original_Weight }}</td>
                            <td>{{ $r->Delivery_Date }}</td>
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
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderRadius: 5
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // 2. PARCEL SIZE CHART
    const sizeRawKeys = {!! json_encode($sizeLabels) !!};
    const sizeRawValues = {!! json_encode($sizeData) !!};
    let groupedSize = { 'Small': 0, 'Other': 0 };
    sizeRawKeys.forEach((id, index) => {
        if (id == 1) groupedSize['Small'] += sizeRawValues[index];
        else groupedSize['Other'] += sizeRawValues[index];
    });

    new Chart(document.getElementById('sizeChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(groupedSize),
            datasets: [{
                data: Object.values(groupedSize),
                backgroundColor: ['#dc3545', '#6c757d']
            }]
        }
    });

    // 3. GENDER CHART
    const genderRaw = {!! json_encode($genderData) !!};
    const genderLabels = genderRaw.map(item => (item.Gender == 1 ? 'Female' : 'Male'));
    const genderCounts = genderRaw.map(item => item.count);

    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#0d6efd', '#fd35b0']
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
                label: 'Parcels',
                data: trendData,
                fill: true,
                tension: 0.3,
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderColor: '#dc3545',
                pointBackgroundColor: '#dc3545'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection