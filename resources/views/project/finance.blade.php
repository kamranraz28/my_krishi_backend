@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
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

                    <div class="card mt-4 shadow-lg">
                        <div class="card-body">
                            <h5 class="mb-4 text-primary font-weight-bold">Financial Details</h5>
                            <div class="d-flex justify-content-end mb-4">
                                <a href="{{ route('printFinanceDetails', ['id' => $project->id]) }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Print Details
                                </a>
                            </div>
                            <hr>

                            <!-- Project Cost & Revenue -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-money-bill-wave mr-3 text-success" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Project Cost</h6>
                                            <p class="text-muted">{{ $totalCost }} BDT</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-hand-holding-usd mr-3 text-primary" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Project Revenue</h6>
                                            <p class="text-muted">{{ $revenue }} BDT</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- Project Outcome & Service Charge -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-chart-line mr-3 text-warning" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Project Outcome</h6>
                                            <p class="text-muted">{{ $profit }} BDT</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-cogs mr-3 text-info" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Service Charge</h6>
                                            <p class="text-muted">{{ $serviceChargePercent }}%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- Net Profit & Total Unit -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-wallet mr-3 text-success" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Net Profit</h6>
                                            <p class="text-muted">{{ $netProfit }} BDT</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-boxes mr-3 text-dark" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Total Unit</h6>
                                            <p class="text-muted">{{ $unit }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <!-- Profit/Unit -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center box-layout p-3 border rounded">
                                        <i class="fas fa-cogs mr-3 text-primary" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h6 class="font-weight-semibold">Profit/Unit</h6>
                                            <p class="text-muted">{{ $profitPerUnit }} BDT</p>
                                        </div>
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
