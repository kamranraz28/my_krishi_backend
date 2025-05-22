@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <div
                class="d-flex justify-content-center align-items-center text-center flex-wrap border rounded shadow-sm overflow-hidden">

                <div class="border-start flex-fill py-3 px-4">
                    <h5 class="mb-1"> Total unit: {{ $project->details->unit }}</h5>
                    <h5 class="mb-1"> Booked unit: {{ $project->details->booked_unit }}</h5>
                    <h5 class="mb-1"> Remaining unit: {{ $remainingUnit }}</h5>
                </div>

                <div class="flex-fill py-5 px-12">
                    <h4 class="display-6 font-weight-bold text-primary mb-1">{{ $project->details->title }}</h4>
                    <h5 class="display-6 font-weight-bold text-primary mb-1">{{ $project->unique_id }}</h5>
                </div>

                <div class="border-start flex-fill py-3 px-4">
                    <h5 class="mb-1">Total Investor: {{ $uniqueTotalInvestors }}</h5>
                    <h5 class="mb-1">Total Agents: {{ $uniqeAgents }}</h5>
                </div>

            </div>
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


                    <div class="card mt-4">
                        <div class="card-body">
                            <h4>Investors:</h4>
                            <table id="example" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
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
                                            <td>
                                                <a href="{{ route('investors.history', $booking->investor->id) }}"
                                                    class="btn btn-info btn-sm d-block">
                                                    {{ $booking->investor->name ?? '' }}
                                                </a>
                                                <span
                                                    class="badge bg-primary mt-1 d-inline-block">{{ $booking->investor->unique_id }}</span>
                                            </td>
                                            <td>{{ $booking->investor->email ?? '' }}</td>
                                            <td>{{ $booking->project->details->unit_price ?? ''}}</td>
                                            <td>{{ $booking->total_unit ?? ''}}</td>
                                            <td>{{ ($booking->project->details->unit_price ?? 0) * ($booking->total_unit ?? 0) }}
                                            </td>
                                            <td>{{ $booking->created_at->format('d M, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Add Agent Button -->
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInvestorModal">
                                Add Investor
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4>Agents:</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Agent Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agents as $key => $agent)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><button type="button" class="btn btn-info btn-sm d-block">
                                                    {{ $agent->user->name ?? '' }}
                                                </button>

                                                <span
                                                    class="badge bg-primary mt-1 d-inline-block">{{ $agent->user->unique_id ?? ''}}</span>
                                            </td>
                                            <td>{{ $agent->user->phone ?? ''}}</td>
                                            <td>{{ $agent->user->email ?? ''}}</td>
                                            <td>
                                                <a href="{{ route('projects.removeAgent', $agent->id) }}"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to activate this investor?');">
                                                    Remove
                                                </a>

                                            </td>


                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Add Agent Button -->
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAgentModal">
                                Add Agent
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Add Agent Modal -->
    <div class="modal fade" id="addAgentModal" tabindex="-1" aria-labelledby="addAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAgentModalLabel">Assign Agent to Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('projects.assignAgent') }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ request()->route('id') }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="agentSelect" class="form-label">Select Agent</label>
                            <select id="agentSelect" name="agent_id" class="form-control" required>
                                <option value="">Select an Agent</option>
                                @foreach($agentList as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}-({{ $agent->unique_id }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Investor Modal -->
    <div class="modal fade" id="addInvestorModal" tabindex="-1" aria-labelledby="addInvestorModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInvestorModal">Add Investor to Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('projects.assignInvestor') }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ request()->route('id') }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="investor_id" class="form-label">Select Investor</label>
                            <select id="investor_id" name="investor_id" class="form-control" required>
                                <option value="">Select an Investor</option>
                                @foreach($investorList as $investor)
                                    <option value="{{ $investor->user->id }}">
                                        {{ $investor->user->name }}-({{ $investor->user->unique_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Number of Unit</label>
                            <input type="number" name="unit" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_note" class="form-label">Note</label>
                            <input type="text" name="payment_note" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Investor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
