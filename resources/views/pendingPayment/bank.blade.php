@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Bank Payment Review</h4>
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



    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project Info</th>
                            <th>Investor Info</th>
                            <th>Units</th>
                            <th>Booking Date</th>
                            <th>Bank Receipt</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm d-block">
                                        {{ $booking->project->details->title ?? '' }}
                                    </a>
                                    <span
                                        class="badge bg-primary mt-1 d-inline-block">{{ $booking->project->unique_id ?? ''}}</span>
                                </td>
                                <td>
                                    <a href="{{ route('investorHistory', $booking->investor_id) }}"
                                        class="btn btn-info btn-sm d-block">
                                        {{ $booking->investor->name ?? '' }}
                                    </a>
                                    <span
                                        class="badge bg-primary mt-1 d-inline-block">{{ $booking->investor->unique_id ?? ''}}</span>
                                </td>
                                <td>{{ $booking->total_unit }}</td>
                                <td>{{ $booking->created_at }}</td>
                                <td>
                                    <a href="{{ route('viewBankReceopt', $booking->id) }}" target="_blank" class="btn btn-secondary btn-sm d-block">
                                    <i class="fas fa-eye me-1"></i> View Receipt
                                    </a>
                                </td>

                                <td>
                                    <a href="#" class="btn btn-info btn-sm d-block">
                                        Pending
                                    </a>
                                </td>

                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('confirmOfficePayment', $booking->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-check-circle me-1"></i> Confirm
                                        </a>
                                        <a href="{{ route('cancelOfficePayment', $booking->id) }}"
                                            onclick="return confirm('Are you sure you want to cancel this booking?')"
                                            class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times-circle me-1"></i> Cancel
                                            </a>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> <!-- /table-responsive -->
        </div>
    </div>



@endsection
