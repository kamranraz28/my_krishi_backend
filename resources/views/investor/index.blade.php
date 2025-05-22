@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Investor List</h4>
        </div>
    </section>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex justify-content-between align-items-center"
            role="alert">
            <span>{{ session('success') }}</span>
            <button type="button" class="btn" data-bs-dismiss="alert" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between align-items-center"
            role="alert">
            <span>{{ session('error') }}</span>
            <button type="button" class="btn" data-bs-dismiss="alert" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Projects Section -->
    <section class="blog-one">
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-md-12">
                    <div class="card">
                        <!-- Card Header: Title Left, Button Right -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Filter By Status</h5>
                            <a href="{{ route('investors.create') }}" class="btn btn-primary">
                                Add Investor
                            </a>
                        </div>

                        <!-- Card Body: Filter Form -->
                        <div class="card-body">
                            <form action="{{ route('investors.filter') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-5">
                                        <select class="form-control" name="status" id="status"
                                            onchange="this.form.submit()">
                                            <option value="1" {{ session('status') == '1' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ session('status') == '2' ? 'selected' : '' }}>Suspened
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>




    <!-- Projects Section -->
    <section class="blog-one">
        <div class="container">

            <div class="row gutter-y-30">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">


                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>NID Number</th>
                                                <th>NID</th>
                                                <th>Bank Details</th>
                                                <th>Blank Check</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($investors as $key => $investor)
                                                                                        <tr>
                                                                                            <td>{{ $key + 1 }}</td>
                                                                                            @php
                                                                                                $hasName = !empty($investor->name);
                                                                                                $hasBookings = $investor->booking && $investor->booking->count() > 0;

                                                                                                $btnClass = 'btn-secondary'; // default

                                                                                                if (!$hasName) {
                                                                                                    $btnClass = 'btn-primary'; // Blue if no name
                                                                                                } elseif ($hasName && !$hasBookings) {
                                                                                                    $btnClass = 'btn-warning'; // Yellow if name but no bookings
                                                                                                } elseif ($hasBookings) {
                                                                                                    $btnClass = 'btn-info'; // Green if bookings exist
                                                                                                }
                                                                                            @endphp

                                                <td>
                                                                                                <a href="{{ route('investors.history', $investor->id) }}"
                                                                                                    class="btn {{ $btnClass }} btn-sm d-block">
                                                                                                    {{ $investor->name ?? 'No Name' }}
                                                                                                </a>
                                                                                                <span
                                                                                                    class="badge bg-primary mt-1 d-inline-block">{{ $investor->unique_id ?? '' }}</span>
                                                                                            </td>


                                                                                            <td>{{ $investor->phone ?? ''}}</td>
                                                                                            <td>{{ $investor->email ?? ''}}</td>
                                                                                            <td>{{ $investor->address ?? ''}}</td>
                                                                                            <td>{{ $investor->investor->nid ?? ''}}</td>
                                                                                            <td>
                                                                                                @if ($investor->investor)
                                                                                                    <a href="{{ route('investors.nid', $investor->investor->id) }}"
                                                                                                        target="_blank" class="btn btn-secondary btn-sm d-block">
                                                                                                        <i class="fas fa-eye me-1"></i> View NID
                                                                                                    </a>
                                                                                                @else
                                                                                                    N/A
                                                                                                @endif
                                                                                            </td>
                                                                                            <td>{{ $investor->investor->bank_details ?? ''}}</td>
                                                                                            <td>
                                                                                                @if ($investor->investor)
                                                                                                    <a href="{{ route('investors.cheque', $investor->investor->id) }}"
                                                                                                        target="_blank" class="btn btn-secondary btn-sm d-block">
                                                                                                        <i class="fas fa-eye me-1"></i> View Check
                                                                                                    </a>
                                                                                                @else
                                                                                                    N/A
                                                                                                @endif
                                                                                            </td>

                                                                                            <td>
                                                                                                @if ($investor->status == 1)
                                                                                                    <a href="{{ route('investors.suspend', $investor->id) }}"
                                                                                                        class="btn btn-danger btn-sm"
                                                                                                        onclick="return confirm('Are you sure you want to suspend this investor?');">
                                                                                                        Suspend
                                                                                                    </a>
                                                                                                @else
                                                                                                    <a href="{{ route('investors.activate', $investor->id) }}"
                                                                                                        class="btn btn-success btn-sm"
                                                                                                        onclick="return confirm('Are you sure you want to activate this investor?');">
                                                                                                        Activate
                                                                                                    </a>

                                                                                                @endif

                                                                                            </td>
                                                                                        </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </section>

@endsection
