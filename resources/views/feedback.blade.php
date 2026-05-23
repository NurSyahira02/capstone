@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Layout --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Customer Feedback</h2>
            <p class="text-muted small mb-0">Service Quality & Sentiment Analysis</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Badge tracking the pristine 35 comments --}}
            <div class="card px-3 py-2 shadow-sm border-0 text-center">
                <div class="text-muted small fw-bold" style="font-size: 0.75rem;">WRITTEN COMMENTS</div>
                <div class="h5 fw-bold mb-0 text-secondary">{{ $feedback->count() }}</div>
            </div>
            {{-- Badge tracking the 52 general rating respondents --}}
            <div class="card px-3 py-2 shadow-sm border-0 text-center">
                <div class="text-muted small fw-bold" style="font-size: 0.75rem;">SURVEY RESPONDENTS</div>
                <div class="h5 fw-bold mb-0 text-danger">{{ $totalResponses }}</div>
            </div>
        </div>
    </div>

    {{-- Clean 4-Column Metric Overview Row (52 Respondents Group) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 text-center shadow-sm border-0 border-top border-primary border-3">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.8rem;">Punctuality Score</div>
                <div class="h3 fw-bold text-dark mb-1">{{ number_format($avgPunctuality, 1) }}<small class="text-muted" style="font-size: 0.9rem;">/5</small></div>
                <div class="text-warning small">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgPunctuality) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center shadow-sm border-0 border-top border-success border-3">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.8rem;">Parcel Condition</div>
                <div class="h3 fw-bold text-dark mb-1">{{ number_format($avgCondition, 1) }}<small class="text-muted" style="font-size: 0.9rem;">/5</small></div>
                <div class="text-warning small">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgCondition) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center shadow-sm border-0 border-top border-info border-3">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.8rem;">Courier Attitude</div>
                <div class="h3 fw-bold text-dark mb-1">{{ number_format($avgAttitude, 1) }}<small class="text-muted" style="font-size: 0.9rem;">/5</small></div>
                <div class="text-warning small">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgAttitude) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center shadow-sm border-0 border-top border-warning border-3">
                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.8rem;">Brand Trust Rating</div>
                <div class="h3 fw-bold text-dark mb-1">{{ number_format($avgTrust, 1) }}<small class="text-muted" style="font-size: 0.9rem;">/5</small></div>
                <div class="text-warning small">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgTrust) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section: Trust Distribution Chart & Pristine Comments Table --}}
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
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Preferred Courier</th>
                                <th>Reasoning</th>
                                <th>Trust</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedback as $f)
                            <tr>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1">{{ $f->preferred_courier }}</span></td>
                                <td class="small text-muted text-wrap" style="max-width: 320px;">{{ $f->reason }}</td>
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