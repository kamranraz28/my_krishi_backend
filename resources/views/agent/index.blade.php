@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Agent List</h4>
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

    <section class="blog-one">
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-md-12">
                    <div class="card">
                        <!-- Card Header: Title Left, Button Right -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Filter By Status</h5>
                            <a href="{{ route('agents.create') }}" class="btn btn-primary">
                                Add Agent
                            </a>
                        </div>

                        <!-- Card Body: Filter Form -->
                        <div class="card-body">
                            <form action="{{ route('agents.filter') }}" method="POST">
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
                                <table class="table" id="example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
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
                                                        {{ $agent->name ?? '' }}
                                                    </button>

                                                    <span
                                                        class="badge bg-primary mt-1 d-inline-block">{{ $agent->unique_id ?? ''}}</span>
                                                </td>
                                                <td>{{ $agent->phone ?? ''}}</td>
                                                <td>{{ $agent->email ?? ''}}</td>

                                                <td>
                                                    @if ($agent->status == 1)
                                                        <a href="{{ route('agents.suspend', $agent->id) }}"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to suspend this agent?');">
                                                            Suspend
                                                        </a>
                                                    @else
                                                        <a href="{{ route('agents.activate', $agent->id) }}"
                                                            class="btn btn-success btn-sm"
                                                            onclick="return confirm('Are you sure you want to activate this agent?');">
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
    </section>




@endsection
