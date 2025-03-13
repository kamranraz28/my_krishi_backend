@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Investor History</h4>
            <h5 class="mb-3">Investor Name: <span class="text-muted">{{ $user->name ?? '' }}</span></h5>
            <h5 class="mb-3">Investor ID: <span class="text-muted">{{ $user->unique_id }}</span></h5>
            <h5 class="mb-3">Journey started with My Krishi:<span class="text-muted"> {{ $user->created_at }}</span></h5>
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
                        <div class="card-header">
                            <div class="card-body">
                                <table class="table" id="example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Project Title</th>
                                            <th>Price/Unit</th>
                                            <th>Number of Units</th>
                                            <th>Total Price</th>
                                            <th>Booking Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $key => $booking)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <!-- <td>
                                                    <a href="{{ route('projectUpdates', $booking->project_id) }}" class="btn btn-info btn-sm">
                                                        {{ $booking->project->details->title }}
                                                    </a>
                                                    <span class="badge bg-primary ms-2">{{ $booking->project->unique_id }}</span>
                                                </td> -->
                                                <td>
                                                    <a href="{{ route('projectUpdates', $booking->project_id) }}" class="btn btn-info btn-sm d-block">
                                                        {{ $booking->project->details->title }}
                                                    </a>
                                                    <span class="badge bg-primary mt-1 d-inline-block">{{ $booking->project->unique_id }}</span>
                                                </td>

                                                <td>{{ $booking->project->details->unit_price }}</td>
                                                <td>{{ $booking->total_unit }}</td>
                                                <td>{{ ($booking->project->details->unit_price ?? 0) * ($booking->total_unit ?? 0) }}</td>
                                                <td>{{ $booking->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </section>



@endsection
