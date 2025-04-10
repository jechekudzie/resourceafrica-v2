@extends('layouts.organisation')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Income Source</h1>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Edit Income Source Record
        </div>
        <div class="card-body">
            <form action="{{ route('organisation.income-sources.update', [$organisation, $incomeSource]) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="period">Year</label>
                            <select class="form-control @error('period') is-invalid @enderror" 
                                    id="period" name="period" required>
                                @php
                                    $currentYear = date('Y');
                                    $years = range(2019, $currentYear);
                                @endphp
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ old('period', $incomeSource->period) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="month">Month</label>
                            <select class="form-control @error('month') is-invalid @enderror" 
                                    id="month" name="month" required>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ (old('month', $incomeSource->month) == $month) ? 'selected' : '' }}>
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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="trophy_fee_amount">Trophy Fee Amount ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('trophy_fee_amount') is-invalid @enderror" 
                                   id="trophy_fee_amount" name="trophy_fee_amount" 
                                   value="{{ old('trophy_fee_amount', number_format($incomeSource->trophy_fee_amount, 2)) }}" required>
                            @error('trophy_fee_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hides_amount">Hides Amount ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('hides_amount') is-invalid @enderror" 
                                   id="hides_amount" name="hides_amount" 
                                   value="{{ old('hides_amount', number_format($incomeSource->hides_amount, 2)) }}" required>
                            @error('hides_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="meat_amount">Meat Amount ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('meat_amount') is-invalid @enderror" 
                                   id="meat_amount" name="meat_amount" 
                                   value="{{ old('meat_amount', number_format($incomeSource->meat_amount, 2)) }}" required>
                            @error('meat_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hunting_concession_fee_amount">Hunting Concession Fee Amount ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('hunting_concession_fee_amount') is-invalid @enderror" 
                                   id="hunting_concession_fee_amount" name="hunting_concession_fee_amount" 
                                   value="{{ old('hunting_concession_fee_amount', number_format($incomeSource->hunting_concession_fee_amount, 2)) }}" required>
                            @error('hunting_concession_fee_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="photographic_fee_amount">Photographic Fee Amount ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('photographic_fee_amount') is-invalid @enderror" 
                                   id="photographic_fee_amount" name="photographic_fee_amount" 
                                   value="{{ old('photographic_fee_amount', number_format($incomeSource->photographic_fee_amount, 2)) }}" required>
                            @error('photographic_fee_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="other_amount">Other Amount ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('other_amount') is-invalid @enderror" 
                                   id="other_amount" name="other_amount" 
                                   value="{{ old('other_amount', number_format($incomeSource->other_amount, 2)) }}" required>
                            @error('other_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="other_description">Other Description</label>
                    <textarea class="form-control @error('other_description') is-invalid @enderror" 
                              id="other_description" name="other_description" rows="3">{{ old('other_description', $incomeSource->other_description) }}</textarea>
                    @error('other_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Update Income Source</button>
                    <a href="{{ route('organisation.income-sources.index', $organisation) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Format number inputs to show 2 decimal places
        $('input[type="number"]').on('change', function() {
            this.value = parseFloat(this.value).toFixed(2);
        });
    });
</script>
@endpush 