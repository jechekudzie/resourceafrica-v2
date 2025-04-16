@extends('layouts.organisation')

@section('title', 'Add Conflict Outcome')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 border-bottom pb-2">
        <div class="row">
            <div class="col-md-8">
                <h1>Add Conflict Outcome</h1>
                <p class="text-muted">
                    <i class="fas fa-info-circle"></i> Adding an outcome to incident: <strong>{{ $wildlifeConflictIncident->title }}</strong>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Incident
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Outcome Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('organisation.wildlife-conflicts.outcomes.store', [$organisation->slug, $wildlifeConflictIncident->id]) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="conflict_outcome_id" class="form-label">Outcome Type</label>
                            <select class="form-select @error('conflict_outcome_id') is-invalid @enderror" id="conflict_outcome_id" name="conflict_outcome_id" required>
                                <option value="">-- Select Outcome Type --</option>
                                @foreach($conflictOutcomes as $outcome)
                                    <option value="{{ $outcome->id }}" {{ old('conflict_outcome_id') == $outcome->id ? 'selected' : '' }}>
                                        {{ $outcome->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('conflict_outcome_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="dynamic-fields-container">
                            <!-- Dynamic fields will be loaded here via JavaScript -->
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Save Outcome
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const outcomeSelect = document.getElementById('conflict_outcome_id');
        const fieldsContainer = document.getElementById('dynamic-fields-container');
        const form = document.querySelector('form');
        const organisationId = "{{ $organisation->id }}";
        
        // Log form submission
        form.addEventListener('submit', function(e) {
            // Prevent default only for debugging if needed
            // e.preventDefault();
            
            console.log('Form is being submitted');
            console.log('Selected outcome ID:', outcomeSelect.value);
            
            // Allow form submission to continue
            // return true;
        });
        
        // Function to load dynamic fields based on selected outcome
        function loadDynamicFields() {
            const outcomeId = outcomeSelect.value;
            
            if (!outcomeId) {
                fieldsContainer.innerHTML = '<div class="alert alert-info">Please select an outcome type to load its dynamic fields.</div>';
                return;
            }
            
            fieldsContainer.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            console.log(`Fetching dynamic fields for outcome ID: ${outcomeId} and organisation ID: ${organisationId}`);
            
            // Use the API route
            axios.get(`/api/conflict-outcomes/${outcomeId}/dynamic-fields/${organisationId}`)
                .then(response => {
                    console.log('Response received:', response);
                    
                    const data = response.data;
                    
                    let fieldsHtml = `
                        <div class="alert alert-info mb-3">
                            <h5 class="alert-heading">Dynamic Fields Information</h5>
                            <p>Fields found: ${data.length}</p>
                        </div>
                    `;
                    
                    if (data.length === 0) {
                        fieldsHtml += '<div class="alert alert-warning">No dynamic fields are associated with this outcome type for your organisation. Add some in the Dynamic Fields section.</div>';
                    } else {
                        fieldsHtml += '<div class="mt-4 border-top pt-4"><h5>Additional Details</h5></div>';
                        
                        data.forEach(field => {
                            fieldsHtml += `
                                <div class="mb-3">
                                    <label for="dynamic_field_${field.id}" class="form-label">${field.label}</label>
                            `;
                            
                            switch (field.field_type) {
                                case 'text':
                                    fieldsHtml += `<input type="text" class="form-control" id="dynamic_field_${field.id}" name="dynamic_field_${field.id}">`;
                                    break;
                                case 'number':
                                    fieldsHtml += `<input type="number" class="form-control" id="dynamic_field_${field.id}" name="dynamic_field_${field.id}">`;
                                    break;
                                case 'textarea':
                                    fieldsHtml += `<textarea class="form-control" id="dynamic_field_${field.id}" name="dynamic_field_${field.id}" rows="3"></textarea>`;
                                    break;
                                case 'select':
                                    fieldsHtml += `<select class="form-select" id="dynamic_field_${field.id}" name="dynamic_field_${field.id}"><option value="">-- Select Option --</option>`;
                                    if (field.options && field.options.length > 0) {
                                        field.options.forEach(option => {
                                            fieldsHtml += `<option value="${option.value}">${option.label}</option>`;
                                        });
                                    }
                                    fieldsHtml += `</select>`;
                                    break;
                                default:
                                    fieldsHtml += `<input type="text" class="form-control" id="dynamic_field_${field.id}" name="dynamic_field_${field.id}">`;
                            }
                            
                            fieldsHtml += `</div>`;
                        });
                    }
                    
                    fieldsContainer.innerHTML = fieldsHtml;
                })
                .catch(error => {
                    console.error('Error details:', error);
                    let errorMessage = 'An unexpected error occurred. Please try again or contact support.';
                    
                    if (error.response) {
                        console.log('Error response:', error.response);
                        errorMessage = `Server error: ${error.response.status} - ${error.response.statusText}`;
                        if (error.response.data && error.response.data.error) {
                            errorMessage = error.response.data.error;
                        }
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    
                    fieldsContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Error loading dynamic fields</h5>
                            <p>${errorMessage}</p>
                            <hr>
                            <p class="mb-0">If this error persists, please ensure dynamic fields are properly configured for this outcome type.</p>
                        </div>
                    `;
                });
        }
        
        // Initial load and event listener
        loadDynamicFields();
        outcomeSelect.addEventListener('change', loadDynamicFields);
    });
</script>
@endpush 