@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa; border-bottom: 3px solid #ddd;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">{{ $project->details->title }}
                ({{ $project->unique_id }})</h4>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="blog-one">
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-md-12">

                    <div class="card mt-4 shadow-lg border-0 rounded-lg">
                        <div class="card-body">
                            <h5 class="mb-4 text-primary font-weight-bold display-5">Financial Details</h5>
                            <div class="d-flex justify-content-end mb-4">
                                <a href="{{ route('projects.printFinanceDetails', ['id' => $project->id]) }}" target="_blank" class="btn btn-primary btn-lg shadow-lg">
                                    <i class="fas fa-print"></i> Print Details
                                </a>
                            </div>
                            <hr class="my-4">

                            <!-- Project Cost & Revenue -->
                            <div class="row text-center">
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-success p-4 rounded-lg">
                                        <i class="fas fa-money-bill-wave mb-3 text-success" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Project Cost</h5>
                                        <p class="text-muted">{{ $totalCost }} BDT</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-primary p-4 rounded-lg">
                                        <i class="fas fa-hand-holding-usd mb-3 text-primary" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Project Revenue</h5>
                                        <p class="text-muted">{{ $revenue }} BDT</p>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">

                            <!-- Project Outcome & Service Charge -->
                            <div class="row text-center">
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-warning p-4 rounded-lg">
                                        <i class="fas fa-chart-line mb-3 text-warning" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Project Outcome</h5>
                                        <p class="text-muted">{{ $profit }} BDT</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-info p-4 rounded-lg">
                                        <i class="fas fa-cogs mb-3 text-info" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Service Charge</h5>
                                        <p class="text-muted">{{ $serviceChargePercent }}%</p>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">

                            <!-- Net Profit & Total Unit -->
                            <div class="row text-center">
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-success p-4 rounded-lg">
                                        <i class="fas fa-wallet mb-3 text-success" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Net Profit</h5>
                                        <p class="text-muted">{{ $netProfit }} BDT</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm border-dark p-4 rounded-lg">
                                        <i class="fas fa-boxes mb-3 text-dark" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Total Unit</h5>
                                        <p class="text-muted">{{ $unit }}</p>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">

                            <!-- Profit/Unit -->
                            <div class="row text-center">
                                <div class="col-md-6 mb-4 mx-auto">
                                    <div class="card shadow-sm border-primary p-4 rounded-lg">
                                        <i class="fas fa-cogs mb-3 text-primary" style="font-size: 2rem;"></i>
                                        <h5 class="font-weight-semibold">Profit/Unit</h5>
                                        <p class="text-muted">{{ $profitPerUnit }} BDT</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
