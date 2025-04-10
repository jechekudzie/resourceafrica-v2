@extends('layouts.organisation')

@section('title', 'Hunting Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Hunting Dashboard</h1>
            <p class="mb-0">Overview of hunting activities and quota utilization</p>
        </div>
        <div>
            <form action="{{ route('organisation.hunting-dashboard', $organisation->slug) }}" method="GET" class="d-flex gap-2">
                <select name="year" class="form-select">
                    @for($y = now()->year - 5; $y <= now()->year; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Activities</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyActivities->flatten()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Concessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $districtActivities->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Species Hunted</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ collect($speciesUtilization)->where('utilized', '>', 0)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paw fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Quota Utilization</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ round(collect($speciesUtilization)->avg('percentage'), 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Monthly Activities Chart -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Activities</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyActivitiesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Species Utilization Chart -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Species Quota Utilization</h6>
                </div>
                <div class="card-body">
                    <canvas id="speciesUtilizationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Concessions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Concession Activities</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="concessionsTable">
                    <thead>
                        <tr>
                            <th>Concession</th>
                            <th>Total Activities</th>
                            <th>Species Count</th>
                            <th>Total Offtake</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($districtActivities as $concession => $data)
                            <tr>
                                <td>{{ $concession }}</td>
                                <td>{{ $data['total_activities'] }}</td>
                                <td>{{ $data['species_count'] }}</td>
                                <td>{{ $data['total_offtake'] }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Activities Chart
    const monthlyCtx = document.getElementById('monthlyActivitiesChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($monthlyActivities->toArray())) !!},
            datasets: [{
                label: 'Number of Activities',
                data: {!! json_encode($monthlyActivities->map->count()->values()) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Species Utilization Chart
    const speciesCtx = document.getElementById('speciesUtilizationChart').getContext('2d');
    new Chart(speciesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($speciesUtilization)->pluck('species')) !!},
            datasets: [{
                label: 'Allocated',
                data: {!! json_encode(collect($speciesUtilization)->pluck('allocated')) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }, {
                label: 'Utilized',
                data: {!! json_encode(collect($speciesUtilization)->pluck('utilized')) !!},
                backgroundColor: 'rgba(28, 200, 138, 0.5)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Initialize DataTables
    $('#concessionsTable').DataTable({
        pageLength: 10,
        ordering: true,
        responsive: true
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
.card-body {
    min-height: 300px;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush 