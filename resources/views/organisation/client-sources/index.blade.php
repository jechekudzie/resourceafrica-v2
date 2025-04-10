@extends('layouts.organisation')

@section('title', 'Client Sources')

@push('head')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Client Sources</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard', $organisation) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Client Sources</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('organisation.client-sources.create', $organisation) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Record
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-list me-1"></i>
                Client Source Records
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover" id="clientSourcesTable">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Month</th>
                            <th>North America</th>
                            <th>Europe & Asia</th>
                            <th>Africa</th>
                            <th>Asia</th>
                            <th>Middle East</th>
                            <th>South America</th>
                            <th>Oceania</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientSources as $source)
                            <tr>
                                <td>{{ $source->period }}</td>
                                <td>{{ $source->month ? date('F', mktime(0, 0, 0, $source->month, 1)) : '-' }}</td>
                                <td>{{ $source->north_america }}</td>
                                <td>{{ $source->europe_asia }}</td>
                                <td>{{ $source->africa }}</td>
                                <td>{{ $source->asia }}</td>
                                <td>{{ $source->middle_east }}</td>
                                <td>{{ $source->south_america }}</td>
                                <td>{{ $source->oceania }}</td>
                                <td>{{ $source->north_america + $source->europe_asia + $source->africa + $source->asia + $source->middle_east + $source->south_america + $source->oceania }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('organisation.client-sources.edit', [$organisation, $source]) }}" 
                                           class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('organisation.client-sources.destroy', [$organisation, $source]) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#clientSourcesTable').DataTable({
            order: [[0, 'desc'], [1, 'desc']],
            pageLength: 25,
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "Showing 0 to 0 of 0 records",
                infoFiltered: "(filtered from _MAX_ total records)"
            }
        });
    });
</script>
@endpush
