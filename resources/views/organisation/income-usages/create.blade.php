@extends('layouts.organisation')

@section('title', 'Add Income Usage')

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
    .page-header .breadcrumb {
        margin-bottom: 0;
        background: transparent;
        padding: 0;
    }
    .page-header .breadcrumb-item a {
        color: #2d5a27;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .form-control:focus {
        border-color: #2d5a27;
        box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Add Income Usage</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard', $organisation) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organisation.income-usages.index', $organisation) }}">Income Usages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add New</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            New Income Usage Record
        </div>
        <div class="card-body">
            <form action="{{ route('organisation.income-usages.store', $organisation) }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="period" class="form-label">Period (Year)</label>
                        <select class="form-control @error('period') is-invalid @enderror" 
                                id="period" name="period" required>
                            @foreach(range(date('Y'), 2019) as $year)
                                <option value="{{ $year }}" {{ old('period') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-control @error('month') is-invalid @enderror" 
                                id="month" name="month" required>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ old('month') == $month ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('month')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="administration_amount" class="form-label">Administration Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('administration_amount') is-invalid @enderror" 
                                   id="administration_amount" name="administration_amount" 
                                   value="{{ old('administration_amount', '0.00') }}" required>
                        </div>
                        @error('administration_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="management_activities_amount" class="form-label">Management Activities Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('management_activities_amount') is-invalid @enderror" 
                                   id="management_activities_amount" name="management_activities_amount" 
                                   value="{{ old('management_activities_amount', '0.00') }}" required>
                        </div>
                        @error('management_activities_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="social_services_amount" class="form-label">Social Services Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('social_services_amount') is-invalid @enderror" 
                                   id="social_services_amount" name="social_services_amount" 
                                   value="{{ old('social_services_amount', '0.00') }}" required>
                        </div>
                        @error('social_services_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="law_enforcement_amount" class="form-label">Law Enforcement Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('law_enforcement_amount') is-invalid @enderror" 
                                   id="law_enforcement_amount" name="law_enforcement_amount" 
                                   value="{{ old('law_enforcement_amount', '0.00') }}" required>
                        </div>
                        @error('law_enforcement_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="other_amount" class="form-label">Other Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('other_amount') is-invalid @enderror" 
                                   id="other_amount" name="other_amount" 
                                   value="{{ old('other_amount', '0.00') }}" required>
                        </div>
                        @error('other_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="other_description" class="form-label">Other Description</label>
                        <input type="text" class="form-control @error('other_description') is-invalid @enderror" 
                               id="other_description" name="other_description" 
                               value="{{ old('other_description') }}" 
                               placeholder="Description for other amount">
                        @error('other_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('organisation.income-usages.index', $organisation) }}" class="btn btn-light me-2">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save Income Usage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 