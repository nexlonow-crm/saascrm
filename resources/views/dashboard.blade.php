@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="row">
    {{-- Open pipeline value --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title text-muted mb-2">Open Pipeline Value</h6>
                <h3 class="mb-0">
                    {{ number_format($openDealsAmount, 2) }}
                </h3>
                <div class="small text-muted">
                    {{ $openDealsCount }} open deals
                </div>
            </div>
        </div>
    </div>

    {{-- Won this month --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title text-muted mb-2">Won This Month</h6>
                <h3 class="mb-0">
                    {{ number_format($wonThisMonthAmount, 2) }}
                </h3>
                <div class="small text-muted">
                    {{ $wonThisMonthCount }} deals won
                </div>
            </div>
        </div>
    </div>

    {{-- Lost this month --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title text-muted mb-2">Lost This Month</h6>
                <h3 class="mb-0">
                    {{ $lostThisMonthCount }}
                </h3>
                <div class="small text-muted">
                    deals lost
                </div>
            </div>
        </div>
    </div>

    {{-- My tasks --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title text-muted mb-2">My Tasks</h6>
                <h3 class="mb-0">
                    {{ $activitiesDueTodayCount }}
                </h3>
                <div class="small text-muted">
                    due today • {{ $activitiesOverdueCount }} overdue
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Row: Deals by stage & Won by month --}}
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Deals by Stage</h5>
            </div>
            <div class="card-body">
                <canvas id="dealsByStageChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Won Amount – Last 6 Months</h5>
            </div>
            <div class="card-body">
                <canvas id="wonByMonthChart" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Row: Activities by type & New contacts --}}
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Activities by Type</h5>
                <span class="small text-muted">My activities</span>
            </div>
            <div class="card-body">
                <canvas id="activitiesByTypeChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">This Week</h5>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>{{ $newContactsThisWeek }}</strong> new contacts created this week.
                </p>
                <p class="text-muted small mb-0">
                    (You can later replace this card with a small table of latest contacts/deals.)
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not loaded');
                return;
            }

            // Deals by stage
            (function () {
                const ctx = document.getElementById('dealsByStageChart');
                if (!ctx) return;

                const labels = @json($stageLabels);
                const counts = @json($stageDealCounts);
                const amounts = @json($stageDealAmounts);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Deals',
                                data: counts,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderWidth: 0
                            },
                            {
                                label: 'Amount',
                                data: amounts,
                                type: 'line',
                                yAxisID: 'y1',
                                borderColor: 'rgba(255, 159, 64, 1)',
                                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            })();

            // Won by month
            (function () {
                const ctx = document.getElementById('wonByMonthChart');
                if (!ctx) return;

                const labels = @json($wonMonthLabels);
                const totals = @json($wonMonthTotals);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Won amount',
                                data: totals,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })();

            // Activities by type
            (function () {
                const ctx = document.getElementById('activitiesByTypeChart');
                if (!ctx) return;

                const labels = @json($activityTypeLabels);
                const totals = @json($activityTypeTotals);

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                data: totals,
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)'
                                ]
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            })();
        });
    </script>
@endpush
