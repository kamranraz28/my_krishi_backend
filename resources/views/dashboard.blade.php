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
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#!">Dashboard Analytics</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- table card-1 start -->
        <div class="col-md-12 col-xl-4">
            <div class="card flat-card">
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="icon feather icon-users text-c-blue mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>1</h5>
                                <span>Users</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="icon feather icon-briefcase text-c-blue mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>2</h5>
                                <span>Field Force</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                            <i class="fas fa-truck text-c-blue mb-1 d-block"></i>

                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>3</h5>
                                <span>Dealer</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="icon feather icon-shopping-cart text-c-blue mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>4</h5>
                                <span>Retail</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- widget primary card start -->
            <div class="card flat-card widget-primary-card">
                <div class="row-table">
                    <div class="col-sm-3 card-body">
                        <i class="feather icon-map"></i>
                    </div>
                    <div class="col-sm-9">
                        <h4>5</h4>
                        <h6>Tracked Locations</h6>
                    </div>
                </div>
            </div>
            <!-- widget primary card end -->
        </div>
        <!-- table card-1 end -->
        <!-- table card-2 start -->
        <div class="col-md-12 col-xl-4">
            <div class="card flat-card">
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                            <i class="fas fa-calendar-day text-c-blue mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>6</h5>
                                <span>Today</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                            <i class="fas fa-calendar text-c-blue mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>7</h5>
                                <span>This Week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="icon feather icon-rotate-ccw text-c-blue mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>8</h5>
                                <span>This Month</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                            <i class="fas fa-clipboard-list text-c-blue"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5>9</h5>
                                <span>This Year</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- widget-success-card start -->
            <div class="card flat-card widget-purple-card">
                <div class="row-table">
                    <div class="col-sm-3 card-body">
                    <!-- <i class="fas fa-clipboard-list"></i> -->
                    <i class="fas fa-user-check"></i>
                    </div>
                    <div class="col-sm-9">
                        <h4>10</h4>
                        <h6>Attendances</h6>
                    </div>
                </div>
            </div>
            <!-- widget-success-card end -->
        </div>
        <!-- table card-2 end -->
        <!-- Widget primary-success card start -->
        <div class="col-md-12 col-xl-4">
            <div class="card support-bar overflow-hidden">
                <div class="card-body pb-0">
                    <h2 class="m-0">11</h2>
                    <span class="text-c-blue">Total Schedule</span>
                    <p class="mb-3 mt-3">Schedule to Field Force visit Retail till today </p>
                </div>
                <div id="support-chart"></div>
                <div class="card-footer bg-primary text-white">
                    <div class="row text-center">
                        <div class="col">
                            <h4 class="m-0 text-white">12</h4>
                            <span>Visited</span>
                        </div>
                        <div class="col">
                            <h4 class="m-0 text-white">13</h4>
                            <span>Today</span>
                        </div>
                        <div class="col">
                            <h4 class="m-0 text-white">14</h4>
                            <span>Upcoming</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Widget primary-success card end -->




@endsection
