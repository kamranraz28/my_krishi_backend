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
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addAgentModal">
                                Add Investor
                            </button>
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
                                                    <td>
                                                        <a href="{{ route('investorHistory', $investor->id) }}"
                                                            class="btn btn-info btn-sm d-block">
                                                            {{ $investor->name ?? '' }}
                                                        </a>
                                                        <span
                                                            class="badge bg-primary mt-1 d-inline-block">{{ $investor->unique_id ?? ''}}</span>
                                                    </td>

                                                    <td>{{ $investor->phone ?? ''}}</td>
                                                    <td>{{ $investor->email ?? ''}}</td>
                                                    <td>{{ $investor->address ?? ''}}</td>
                                                    <td>{{ $investor->investor->nid ?? ''}}</td>
                                                    <td>
                                                        @if ($investor->investor)
                                                            <a href="{{ route('viewNid', $investor->investor->id) }}"
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
                                                            <a href="{{ route('viewCheck', $investor->investor->id) }}"
                                                                target="_blank" class="btn btn-secondary btn-sm d-block">
                                                                <i class="fas fa-eye me-1"></i> View Check
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <form action="{{ route('investorDelete', $investor->id) }}"
                                                            method="POST" style="display: inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this investor?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                Delete
                                                            </button>
                                                        </form>
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

    <!-- Add Agent Modal -->
    <div class="modal fade" id="addAgentModal" tabindex="-1" aria-labelledby="addAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAgentModalLabel">Create New Investor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('investorStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Investor Name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone"
                                placeholder="Enter Investor Phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email"
                                placeholder="Enter Investor Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" name="address" id="address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>
                        <div class="mb-3">
                            <label for="nid" class="form-label">NID Number</label>
                            <input type="number" class="form-control" name="nid" id="nid"
                                placeholder="Enter Investor NID Number">
                        </div>
                        <div class="mb-3">
                            <label for="nid_upload" class="form-label">NID Upload</label>
                            <input type="file" class="form-control" name="nid_upload" id="nid_upload">
                        </div>

                        <div class="mb-3">
                            <label for="acc_name" class="form-label">Bank Details</label>
                            <select class="form-control" name="acc_name" id="acc_name">
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="acc_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" name="acc_name" id="acc_name"
                                placeholder="Enter Investor Account Name">
                        </div>

                        <div class="mb-3">
                            <label for="acc_number" class="form-label">Account Number</label>
                            <input type="number" class="form-control" name="acc_number" id="acc_number"
                                placeholder="Enter Investor Account Number">
                        </div>

                        <div class="mb-3">
                            <label for="branch_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control" name="branch_name" id="branch_name"
                                placeholder="Enter Branch Name">
                        </div>

                        <div class="mb-3">
                            <label for="routing_number" class="form-label">Routing Number</label>
                            <input type="text" class="form-control" name="routing_number" id="routing_number"
                                placeholder="Enter Routing Number">
                        </div>

                        <div class="mb-3">
                            <label for="swift_code" class="form-label">Swift Code</label>
                            <input type="number" class="form-control" name="swift_code" id="swift_code"
                                placeholder="Enter Swift Code">
                        </div>

                        <div class="mb-3">
                            <label for="check_upload" class="form-label">Blank Check Upload</label>
                            <input type="file" class="form-control" name="check_upload" id="check_upload">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm Investor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
