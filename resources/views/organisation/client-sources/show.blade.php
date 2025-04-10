@extends('layouts.organisation')

@section('title', 'Client Source Details')

@push('head')
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: #2d5a27 !important;
        color: white !important;
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .btn-primary, .btn-success {
        background-color: #2d5a27 !important;
        border-color: #2d5a27 !important;
    }
    .btn-primary:hover, .btn-success:hover {
        background-color: #1e3d1a !important;
        border-color: #1e3d1a !important;
    }
    .page-header {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .page-header h1 {
        margin-bottom: 0.5rem;
        color: #2d5a27;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
    }
    .info-value {
        color: #212529;
    }
    .info-card {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    .total-section {
        background-color: #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    .total-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d5a27;
    }
    .region-stats {
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem 0;
    }
    .region-stats:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Client Source Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard', $organisation) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organisation.client-sources.index', $organisation) }}">Client Sources</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Record</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('organisation.client-sources.edit', [$organisation, $clientSource]) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Record
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Client Source Information
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5 class="mb-3">Period Information</h5>
                        <div class="mb-2">
                            <span class="info-label">Year:</span>
                            <span class="info-value ms-2">{{ $clientSource->period }}</span>
                        </div>
                        <div>
                            <span class="info-label">Month:</span>
                            <span class="info-value ms-2">{{ date('F', mktime(0, 0, 0, $clientSource->month, 1)) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h5 class="mb-3">Record Information</h5>
                        <div class="mb-2">
                            <span class="info-label">Created:</span>
                            <span class="info-value ms-2">{{ $clientSource->created_at->format('F j, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="info-label">Last Updated:</span>
                            <span class="info-value ms-2">{{ $clientSource->updated_at->format('F j, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Regional Distribution</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">North America</span>
                                    <span class="info-value">{{ $clientSource->north_america }}</span>
                                </div>
                            </div>
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">Europe & Asia</span>
                                    <span class="info-value">{{ $clientSource->europe_asia }}</span>
                                </div>
                            </div>
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">Africa</span>
                                    <span class="info-value">{{ $clientSource->africa }}</span>
                                </div>
                            </div>
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">Asia</span>
                                    <span class="info-value">{{ $clientSource->asia }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">Middle East</span>
                                    <span class="info-value">{{ $clientSource->middle_east }}</span>
                                </div>
                            </div>
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">South America</span>
                                    <span class="info-value">{{ $clientSource->south_america }}</span>
                                </div>
                            </div>
                            <div class="region-stats">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-label">Oceania</span>
                                    <span class="info-value">{{ $clientSource->oceania }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="total-section mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="info-label">Total Clients</span>
                            <span class="total-value">{{ $clientSource->total_clients }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('organisation.client-sources.index', $organisation) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                <form action="{{ route('organisation.client-sources.destroy', [$organisation, $clientSource]) }}" 
                      method="POST" class="d-inline ms-2"
                      onsubmit="return confirm('Are you sure you want to delete this record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 