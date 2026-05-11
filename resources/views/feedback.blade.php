@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Customer Feedback</h2>
            <p class="text-muted small mb-0">Service Quality & Sentiment Analysis</p>
        </div>
        <div class="card px-3 py-2 shadow-sm border-0 text-center">
            <div class="text-muted small fw-bold">TOTAL RESPONDENTS</div>
            <div class="h4 fw-bold mb-0 text-danger">{{ $feedback->count() }}</div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-4 text-center shadow-sm border-0">
                <div class="text-muted small fw-bold text-uppercase mb-2">Punctuality Score</div>
                <div class="display-6 fw-bold text-dark">{{ number_format($avgPunctuality, 1) }}<small class="text-muted" style="font-size: 1rem;">/5</small></div>
                <div class="text-warning mt-1">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgPunctuality) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center shadow-sm border-0">
                <div class="text-muted small fw-bold text-uppercase mb-2">Parcel Condition</div>
                <div class="display-6 fw-bold text-dark">{{ number_format($avgCondition, 1) }}<small class="text-muted" style="font-size: 1rem;">/5</small></div>
                <div class="text-warning mt-1">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgCondition) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center shadow-sm border-0">
                <div class="text-muted small fw-bold text-uppercase mb-2">Courier Attitude</div>
                <div class="display-6 fw-bold text-dark">{{ number_format($avgAttitude, 1) }}<small class="text-muted" style="font-size: 1rem;">/5</small></div>
                <div class="text-warning mt-1">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgAttitude) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card p-4 h-100 shadow-sm border-0">
                <h5 class="fw-bold mb-4">NinjaVan Trust Level</h5>
                <div style="height: 300px;">
                    <canvas id="trustChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card p-4 h-100 shadow-sm border-0">
                <h5 class="fw-bold mb-4">Recent Comments & Reasons</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Preferred Courier</th>
                                <th>Reasoning</th>
                                <th>Trust</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedback as $f)
                            <tr>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger px-3">{{ $f->preferred_courier }}</span></td>
                                <td class="small text-muted">{{ $f->reason }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $f->trust_rating }}</span><span class="text-muted small">/5</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Trust Chart Logic
    const trustLabels = {!! json_encode($trustLabels) !!};
    const trustData = {!! json_encode($trustData) !!};

    new Chart(document.getElementById('trustChart'), {
        type: 'bar',
        data: {
            labels: trustLabels.map(l => 'Level ' + l),
            datasets: [{
                label: 'Respondents',
                data: trustData,
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1 },
                    grid: { display: false }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection