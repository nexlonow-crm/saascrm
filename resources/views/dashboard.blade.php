@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    {{-- Total Contacts --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col">
                        <div class="text-muted">Contacts</div>
                        <div class="h2 mb-0">{{ $contactsCount }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-primary">
                            <i class="align-middle" data-feather="users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Companies --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col">
                        <div class="text-muted">Companies</div>
                        <div class="h2 mb-0">{{ $companiesCount }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-success">
                            <i class="align-middle" data-feather="briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Open Deals --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col">
                        <div class="text-muted">Open Deals</div>
                        <div class="h2 mb-0">{{ $openDealsCount }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-warning">
                            <i class="align-middle" data-feather="dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Won Deals This Month --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row g-0 align-items-center">
                    <div class="col">
                        <div class="text-muted">Won This Month</div>
                        <div class="h2 mb-0">{{ $wonDealsThisMonth }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat text-info">
                            <i class="align-middle" data-feather="award"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Second row: pipeline value, etc. --}}
<div class="row">
    <div class="col-md-6">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0">Pipeline Value (Open Deals)</h5>
            </div>
            <div class="card-body">
                <div class="display-6">
                    {{ number_format($pipelineValueOpen, 2) }}
                </div>
                <div class="text-muted">
                    Total amount of open deals in your current CRM.
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder for future chart / stats --}}
    <div class="col-md-6">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0">Activity Overview</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">
                    You can later add charts or recent activity here (calls, tasks, emails).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
