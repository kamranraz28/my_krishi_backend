@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard Analytics</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Table Card 1 Start -->
        <div class="col-md-12 col-xl-4">
            <div class="card flat-card">
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-users text-c-blue mb-1 d-block"></i> <!-- Users Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $totalUsers }}</h5>
                                <span>Users</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-user-tie text-c-blue mb-1 d-block"></i> <!-- Agents Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $agents }}</h5>
                                <span>Agents</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-user-shield text-c-blue mb-1 d-block"></i> <!-- Investors Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $investors }}</h5>
                                <span>Investors</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-user-cog text-c-blue mb-1 d-block"></i> <!-- Others/Admin Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $admin }}</h5>
                                <span>Others</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget Primary Card Start -->
            <div class="card flat-card widget-primary-card">
                <div class="row-table">
                    <div class="col-sm-3 card-body">
                        <i class="fas fa-building text-c-white"></i> <!-- Unit Icon -->
                    </div>
                    <div class="col-sm-9">
                        <h4>{{ $totalUnits }}</h4>
                        <h6>Unit Booked</h6>
                    </div>
                </div>
            </div>
            <!-- Widget Primary Card End -->
        </div>
        <!-- Table Card 1 End -->

        <!-- Table Card 2 Start -->
        <div class="col-md-12 col-xl-4">
            <div class="card flat-card">
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-calendar-alt text-c-blue mb-1 d-block"></i> <!-- Total Bookings Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $totalBooking }}</h5>
                                <span>Bookings</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-calendar-day text-c-blue mb-1 d-block"></i> <!-- Today's Booking Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $todayBooking }}</h5>
                                <span>Today</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-calendar-week text-c-blue mb-1 d-block"></i>
                                <!-- This Month's Booking Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $thisMonthBooking }}</h5>
                                <span>This Month</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-calendar-check text-c-blue"></i> <!-- This Year's Booking Icon -->
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>{{ $thisYearBooking }}</h5>
                                <span>This Year</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Widget Success Card Start -->
            <div class="card flat-card widget-purple-card">
                <div class="row-table">
                    <div class="col-sm-3 card-body">
                        <i class="fas fa-money-bill-wave text-c-white"></i> <!-- Total Investment Icon -->
                    </div>
                    <div class="col-sm-9">
                        <h4>{{ $totalAmount }} BDT</h4>
                        <h6>Total Investment</h6>
                    </div>
                </div>
            </div>
            <!-- Widget Success Card End -->
        </div>
        <!-- Table Card 2 End -->

        <!-- Widget Primary Success Card Start -->
        <div class="col-md-12 col-xl-4">
            <div class="card support-bar overflow-hidden">
                <div class="card-body pb-0">
                    <h2 class="m-0">{{ $totalProject }}</h2>
                    <span class="text-c-blue">Total Project</span>
                    <p class="mb-3 mt-3">Projects available in the system till today.</p>
                </div>
                <div id="support-chart"></div>
                <div class="card-footer bg-primary text-white">
                    <div class="row text-center">
                        <div class="col">
                            <h4 class="m-0 text-white">{{ $draftProjects }}</h4>
                            <span>Draft</span>
                        </div>
                        <div class="col">
                            <h4 class="m-0 text-white">{{ $runningProjects }}</h4>
                            <span>Running</span>
                        </div>
                        <div class="col">
                            <h4 class="m-0 text-white">{{ $completedProjects }}</h4>
                            <span>Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Widget Primary Success Card End -->
    </div>





@endsection