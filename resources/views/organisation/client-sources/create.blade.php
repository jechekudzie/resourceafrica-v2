@extends('layouts.organisation')

@section('title', 'Add Client Source')

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
    .form-label.required::after {
        content: "*";
        color: #dc3545;
        margin-left: 4px;
    }
    .form-control:focus {
        border-color: #2d5a27;
        box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
    }
    .form-select:focus {
        border-color: #2d5a27;
        box-shadow: 0 0 0 0.25rem rgba(45, 90, 39, 0.25);
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    .invalid-feedback {
        font-size: 0.875rem;
    }
    .help-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Add New Client Source</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard', $organisation) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organisation.client-sources.index', $organisation) }}">Client Sources</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add New Record</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            New Client Source Record
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please check the form for errors.</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('organisation.client-sources.store', $organisation) }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="period" class="form-label required">Year</label>
                            <select class="form-select @error('period') is-invalid @enderror" id="period" name="period" required>
                                @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 5;
                                    $endYear = $currentYear + 1;
                                @endphp
                                @for($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}" {{ old('period') == $year ? 'selected' : ($year == $currentYear ? 'selected' : '') }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="month" class="form-label required">Month</label>
                            <select class="form-select @error('month') is-invalid @enderror" id="month" name="month" required>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ old('month') == $month ? 'selected' : ($month == date('n') ? 'selected' : '') }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="north_america" class="form-label required">North America</label>
                            <input type="number" class="form-control @error('north_america') is-invalid @enderror" 
                                   id="north_america" name="north_america" 
                                   value="{{ old('north_america', 0) }}" 
                                   min="0" step="1" required>
                            @error('north_america')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from North America</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="europe_asia" class="form-label required">Europe & Asia</label>
                            <input type="number" class="form-control @error('europe_asia') is-invalid @enderror" 
                                   id="europe_asia" name="europe_asia" 
                                   value="{{ old('europe_asia', 0) }}" 
                                   min="0" step="1" required>
                            @error('europe_asia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from Europe & Asia</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="africa" class="form-label required">Africa</label>
                            <input type="number" class="form-control @error('africa') is-invalid @enderror" 
                                   id="africa" name="africa" 
                                   value="{{ old('africa', 0) }}" 
                                   min="0" step="1" required>
                            @error('africa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from Africa</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="asia" class="form-label required">Asia</label>
                            <input type="number" class="form-control @error('asia') is-invalid @enderror" 
                                   id="asia" name="asia" 
                                   value="{{ old('asia', 0) }}" 
                                   min="0" step="1" required>
                            @error('asia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from Asia</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="middle_east" class="form-label required">Middle East</label>
                            <input type="number" class="form-control @error('middle_east') is-invalid @enderror" 
                                   id="middle_east" name="middle_east" 
                                   value="{{ old('middle_east', 0) }}" 
                                   min="0" step="1" required>
                            @error('middle_east')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from Middle East</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="south_america" class="form-label required">South America</label>
                            <input type="number" class="form-control @error('south_america') is-invalid @enderror" 
                                   id="south_america" name="south_america" 
                                   value="{{ old('south_america', 0) }}" 
                                   min="0" step="1" required>
                            @error('south_america')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from South America</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="oceania" class="form-label required">Oceania</label>
                            <input type="number" class="form-control @error('oceania') is-invalid @enderror" 
                                   id="oceania" name="oceania" 
                                   value="{{ old('oceania', 0) }}" 
                                   min="0" step="1" required>
                            @error('oceania')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Enter the number of clients from Oceania</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Record
                    </button>
                    <a href="{{ route('organisation.client-sources.index', $organisation) }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Auto-calculate total
    function calculateTotal() {
        const fields = [
            'north_america', 'europe_asia', 'africa', 'asia',
            'middle_east', 'south_america', 'oceania'
        ];
        let total = 0;
        fields.forEach(field => {
            const value = parseInt(document.getElementById(field).value) || 0;
            total += value;
        });
        document.getElementById('total').textContent = total;
    }

    // Add event listeners to all number inputs
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Calculate initial total
    calculateTotal();
</script>
@endpush 