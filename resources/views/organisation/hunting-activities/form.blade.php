@extends('layouts.organisation')

@section('title', isset($huntingActivity) ? 'Edit Hunting Activity' : 'Create Hunting Activity')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ isset($huntingActivity) ? 'Edit Hunting Activity' : 'Create Hunting Activity' }}</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ isset($huntingActivity) 
                        ? route('organisation.hunting-activities.update', [$organisation->slug, $huntingActivity->id])
                        : route('organisation.hunting-activities.store', $organisation->slug) }}" 
                          method="POST">
                        @csrf
                        @if(isset($huntingActivity))
                            @method('PATCH')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hunting_concession_id" class="form-label">Hunting Concession</label>
                                    <select name="hunting_concession_id" id="hunting_concession_id" class="form-select @error('hunting_concession_id') is-invalid @enderror" required>
                                        <option value="">Select Hunting Concession</option>
                                        @foreach($huntingConcessions as $concession)
                                            <option value="{{ $concession->id }}" 
                                                {{ (old('hunting_concession_id', $huntingActivity->hunting_concession_id ?? '') == $concession->id) ? 'selected' : '' }}>
                                                {{ $concession->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hunting_concession_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="safari_id" class="form-label">Safari Operator</label>
                                    <select name="safari_id" id="safari_id" class="form-select @error('safari_id') is-invalid @enderror" required>
                                        <option value="">Select Safari Operator</option>
                                        @foreach($safariOperators as $operator)
                                            <option value="{{ $operator->id }}"
                                                {{ (old('safari_id', $huntingActivity->safari_id ?? '') == $operator->id) ? 'selected' : '' }}>
                                                {{ $operator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('safari_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="text" 
                                           class="form-control datetimepicker @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date', isset($huntingActivity) ? $huntingActivity->start_date->format('Y-m-d') : '') }}"
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="text" 
                                           class="form-control datetimepicker @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date', isset($huntingActivity) ? $huntingActivity->end_date->format('Y-m-d') : '') }}"
                                           required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="period" class="form-label">Quota Period (Year)</label>
                                    <select class="form-select @error('period') is-invalid @enderror" 
                                            id="period" 
                                            name="period" 
                                            required>
                                        <option value="">Select Period</option>
                                        @php
                                            $currentYear = date('Y');
                                            $startYear = $currentYear - 2;
                                            $endYear = $currentYear + 2;
                                        @endphp
                                        @for($year = $startYear; $year <= $endYear; $year++)
                                            <option value="{{ $year }}" {{ (old('period', isset($huntingActivity) ? $huntingActivity->period : $currentYear) == $year) ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Professional Hunter Licenses Section -->
                        <div class="mb-4">
                            <h5>Professional Hunter Licenses</h5>
                            <div id="professional-hunter-licenses">
                                @if(isset($huntingActivity) && $huntingActivity->professionalHunterLicenses->count() > 0)
                                    @foreach($huntingActivity->professionalHunterLicenses as $index => $license)
                                        <div class="row mb-3 professional-hunter-license">
                                            <div class="col-md-5">
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="professional_hunter_licenses[{{ $index }}][license_number]" 
                                                       placeholder="License Number"
                                                       value="{{ $license->license_number }}">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="professional_hunter_licenses[{{ $index }}][hunter_name]" 
                                                       placeholder="Hunter Name"
                                                       value="{{ $license->hunter_name }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-license">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row mb-3 professional-hunter-license">
                                        <div class="col-md-5">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="professional_hunter_licenses[0][license_number]" 
                                                   placeholder="License Number">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="professional_hunter_licenses[0][hunter_name]" 
                                                   placeholder="Hunter Name">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-license">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-license">
                                <i class="fas fa-plus"></i> Add License
                            </button>
                        </div>

                        <!-- Species Section -->
                        <div class="mb-4">
                            <h5>Species and Off-take</h5>
                            <div id="species-list">
                                @if(isset($huntingActivity) && $huntingActivity->species->count() > 0)
                                    @foreach($huntingActivity->species as $index => $speciesItem)
                                        <div class="row mb-3 species-item">
                                            <div class="col-md-4">
                                                <select class="form-select species-select" 
                                                        name="species[{{ $index }}][id]" 
                                                        data-index="{{ $index }}"
                                                        required>
                                                    <option value="">Select Species</option>
                                                    @foreach($species as $s)
                                                        <option value="{{ $s->id }}" 
                                                            {{ $speciesItem->id == $s->id ? 'selected' : '' }}
                                                            data-id="{{ $s->id }}">
                                                            {{ $s->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <input type="number" 
                                                           class="form-control off-take-input" 
                                                           name="species[{{ $index }}][off_take]" 
                                                           placeholder="Off-take"
                                                           data-index="{{ $index }}"
                                                           value="{{ $speciesItem->pivot->off_take }}"
                                                           min="1"
                                                           required>
                                                    <span class="input-group-text">animals</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="quota-info" id="quota-info-{{ $index }}">
                                                    <div class="card">
                                                        <div class="card-body p-2 text-center">
                                                            <span class="fw-bold d-block mb-1 small text-muted">Remaining Quota</span>
                                                            <span class="quota-remaining-value fs-4 fw-bold">--</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-species">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row mb-3 species-item">
                                        <div class="col-md-4">
                                            <select class="form-select species-select" 
                                                    name="species[0][id]" 
                                                    data-index="0"
                                                    required>
                                                <option value="">Select Species</option>
                                                @foreach($species as $speciesItem)
                                                    <option value="{{ $speciesItem->id }}" data-id="{{ $speciesItem->id }}">{{ $speciesItem->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control off-take-input" 
                                                       name="species[0][off_take]" 
                                                       data-index="0"
                                                       placeholder="Off-take"
                                                       min="1"
                                                       required>
                                                <span class="input-group-text">animals</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                                    <div class="quota-info" id="quota-info-0">
                                                        <div class="card">
                                                            <div class="card-body p-2 text-center">
                                                                <span class="fw-bold d-block mb-1 small text-muted">Remaining Quota</span>
                                                                <span class="quota-remaining-value fs-4 fw-bold">--</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-species">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-species">
                                <i class="fas fa-plus"></i> Add Species
                            </button>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($huntingActivity) ? 'Update Hunting Activity' : 'Create Hunting Activity' }}
                            </button>
                            <a href="{{ route('organisation.hunting-activities.index', $organisation->slug) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .quota-remaining-value {
        font-size: 1.5rem !important;
        font-weight: bold !important;
        display: block;
    }
    .quota-remaining-value.text-danger {
        color: #dc3545 !important;
    }
    .quota-remaining-value.text-success {
        color: #198754 !important;
    }
    .card-body.error {
        background-color: rgba(220, 53, 69, 0.1);
        border-left: 3px solid #dc3545;
    }
    .card-body.success {
        background-color: rgba(25, 135, 84, 0.1);
        border-left: 3px solid #198754;
    }
    .quota-info .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    input.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for better dropdown experience
        $('.species-select').select2();

        // Handle adding new professional hunter license
        let licenseCount = $('.professional-hunter-license').length;
        $('#add-license').click(function() {
            const newLicense = `
                <div class="row mb-3 professional-hunter-license">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="professional_hunter_licenses[${licenseCount}][license_number]" placeholder="License Number">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="professional_hunter_licenses[${licenseCount}][hunter_name]" placeholder="Hunter Name">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-license">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#professional-hunter-licenses').append(newLicense);
            licenseCount++;
        });

        // Handle removing professional hunter license
        $(document).on('click', '.remove-license', function() {
            if ($('.professional-hunter-license').length > 1) {
                $(this).closest('.professional-hunter-license').remove();
            } else {
                alert('At least one professional hunter license is required.');
            }
        });

        // Handle adding new species
        let speciesCount = $('.species-item').length;
        $('#add-species').click(function() {
            const newSpecies = `
                <div class="row mb-3 species-item">
                    <div class="col-md-4">
                        <select class="form-select species-select new-select" name="species[${speciesCount}][id]" data-index="${speciesCount}" required>
                            <option value="">Select Species</option>
                            @foreach($species as $speciesItem)
                                <option value="{{ $speciesItem->id }}" data-id="{{ $speciesItem->id }}">{{ $speciesItem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="number" class="form-control off-take-input" name="species[${speciesCount}][off_take]" data-index="${speciesCount}" placeholder="Off-take" min="1" required>
                            <span class="input-group-text">animals</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quota-info" id="quota-info-${speciesCount}">
                            <div class="card">
                                <div class="card-body p-2 text-center">
                                    <span class="fw-bold d-block mb-1 small text-muted">Remaining Quota</span>
                                    <span class="quota-remaining-value fs-4 fw-bold">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-species">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#species-list').append(newSpecies);
            $('.new-select').select2();
            $('.new-select').removeClass('new-select');
            speciesCount++;
        });

        // Handle removing species
        $(document).on('click', '.remove-species', function() {
            if ($('.species-item').length > 1) {
                $(this).closest('.species-item').remove();
            } else {
                alert('At least one species is required.');
            }
        });

        // Get the period from the dropdown
        function getPeriod() {
            const period = $('#period').val();
            if (period) {
                return period;
            }
            return null;
        }

        // Get quota allocation for species
        async function getQuotaAllocation(speciesId, index) {
            const period = getPeriod();
            if (!period) {
                alert('Please select a quota period first.');
                return;
            }

            try {
                const response = await fetch(`/api/organisations/{{ $organisation->slug }}/quota-allocations?species_id=${speciesId}&period=${period}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Quota data:', data);
                
                const quotaInfoElement = $(`#quota-info-${index}`);
                const offTakeInput = $(`.off-take-input[data-index="${index}"]`);
                const remainingElement = quotaInfoElement.find('.quota-remaining-value');
                
                let remainingQuota = 0;
                
                if (data.quota_allocation) {
                    // The API returns remaining_quota directly
                    remainingQuota = data.quota_balance || 0;
                }
                
                // Update the quota information display
                remainingElement.text(`${remainingQuota}`);
                
                // Store the available quota as a data attribute for validation
                offTakeInput.attr('data-available-quota', remainingQuota);
                offTakeInput.attr('max', remainingQuota);
                
                if (remainingQuota > 0) {
                    remainingElement.addClass('text-success').removeClass('text-danger');
                    quotaInfoElement.find('.card-body').addClass('success').removeClass('error');
                } else {
                    remainingElement.addClass('text-danger').removeClass('text-success');
                    quotaInfoElement.find('.card-body').addClass('error').removeClass('success');
                }
                
                validateOffTake(index);
            } catch (error) {
                console.error('Error fetching quota allocation:', error);
            }
        }

        // Automatically set period based on start date when new activity
        $('#start_date').on('change', function() {
            if (!$('#period').val()) {
                const startDate = $(this).val();
                if (startDate) {
                    const year = new Date(startDate).getFullYear();
                    $('#period').val(year).trigger('change');
                }
            }
        });

        // Trigger all quota validations when period changes
        $('#period').on('change', function() {
            $('.species-select').each(function() {
                const index = $(this).data('index');
                const speciesId = $(this).val();
                
                if (speciesId) {
                    getQuotaAllocation(speciesId, index);
                }
            });
        });

        // Listen for changes in off-take input - validate immediately
        $(document).on('input', '.off-take-input', function() {
            const index = $(this).data('index');
            validateOffTake(index);
        });

        // Also validate on blur to catch any missed validations
        $(document).on('blur', '.off-take-input', function() {
            const index = $(this).data('index');
            validateOffTake(index);
        });

        // Validate off-take against available quota
        function validateOffTake(index) {
            const offTakeInput = $(`.off-take-input[data-index="${index}"]`);
            const availableQuota = parseInt(offTakeInput.attr('data-available-quota') || 0);
            const offTakeValue = parseInt(offTakeInput.val() || 0);
            const quotaInfoElement = $(`#quota-info-${index}`);
            const remainingElement = quotaInfoElement.find('.quota-remaining-value');
            
            console.log(`Validating offtake: value=${offTakeValue}, available=${availableQuota}`);
            
            if (offTakeValue > availableQuota) {
                // Off-take exceeds available quota
                offTakeInput.addClass('is-invalid');
                quotaInfoElement.find('.card-body').addClass('error').removeClass('success');
                remainingElement.addClass('text-danger').removeClass('text-success');
                return false;
            } else if (offTakeValue > 0) {
                // Valid off-take
                offTakeInput.removeClass('is-invalid');
                quotaInfoElement.find('.card-body').addClass('success').removeClass('error');
                remainingElement.addClass('text-success').removeClass('text-danger');
                
                // Update the remaining display to show what will be left
                const remaining = availableQuota - offTakeValue;
                remainingElement.text(`${remaining}`);
                
                return true;
            } else {
                // No off-take entered yet
                offTakeInput.removeClass('is-invalid');
                quotaInfoElement.find('.card-body').removeClass('success').removeClass('error');
                remainingElement.removeClass('text-success text-danger');
                remainingElement.text(`${availableQuota}`);
                return true;
            }
        }

        // Initial setup for existing species
        $('.species-select').each(function() {
            const index = $(this).data('index');
            const speciesId = $(this).val();
            
            if (speciesId) {
                // When editing, we need to set a small delay to ensure the period field is populated
                setTimeout(() => {
                    getQuotaAllocation(speciesId, index);
                }, 300);
            }
        });

        // For edit mode, ensure we validate existing off-take values against quota
        if ($('input[name="_method"]').val() === 'PATCH') {
            $('.off-take-input').each(function() {
                const index = $(this).data('index');
                setTimeout(() => {
                    validateOffTake(index);
                }, 500);
            });
        }

        // Listen for changes in species selection
        $(document).on('change', '.species-select', function() {
            const speciesId = $(this).val();
            const index = $(this).data('index');
            
            if (speciesId) {
                getQuotaAllocation(speciesId, index);
            }
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            let isValid = true;
            
            // Check each off-take against available quota
            $('.off-take-input').each(function() {
                const index = $(this).data('index');
                if (!validateOffTake(index)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please correct the off-take values that exceed available quota.');
            }
        });
    });
</script>
@endpush
@endsection 