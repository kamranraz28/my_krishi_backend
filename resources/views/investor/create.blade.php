@extends('layouts.master')

@section('content')

<!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Add New Investor</h4>
        </div>
    </section>

<div class="container mt-5">
    <form action="{{ route('investors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Investor Name" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Investor Phone" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Enter Investor Email" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" name="address" id="address" placeholder="Enter Address"></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" name="image" id="image">
        </div>

        <div class="mb-3">
            <label for="nid" class="form-label">NID Number</label>
            <input type="number" class="form-control" name="nid" id="nid" placeholder="Enter NID Number">
        </div>

        <div class="mb-3">
            <label for="nid_upload" class="form-label">NID Upload</label>
            <input type="file" class="form-control" name="nid_upload" id="nid_upload">
        </div>

        <div class="mb-3">
            <label for="bank_id" class="form-label">Bank</label>
            <select class="form-control" name="bank_id" id="bank_id">
                <option value="">Select Bank</option>
                @foreach ($banks as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="acc_name" class="form-label">Account Name</label>
            <input type="text" class="form-control" name="acc_name" id="acc_name" placeholder="Enter Account Name">
        </div>

        <div class="mb-3">
            <label for="acc_number" class="form-label">Account Number</label>
            <input type="number" class="form-control" name="acc_number" id="acc_number" placeholder="Enter Account Number">
        </div>

        <div class="mb-3">
            <label for="branch_name" class="form-label">Branch Name</label>
            <input type="text" class="form-control" name="branch_name" id="branch_name" placeholder="Enter Branch Name">
        </div>

        <div class="mb-3">
            <label for="routing_number" class="form-label">Routing Number</label>
            <input type="text" class="form-control" name="routing_number" id="routing_number" placeholder="Enter Routing Number">
        </div>

        <div class="mb-3">
            <label for="swift_code" class="form-label">Swift Code</label>
            <input type="number" class="form-control" name="swift_code" id="swift_code" placeholder="Enter Swift Code">
        </div>

        <div class="mb-3">
            <label for="check_upload" class="form-label">Blank Check Upload</label>
            <input type="file" class="form-control" name="check_upload" id="check_upload">
        </div>

        <button type="submit" class="btn btn-primary">Confirm Investor</button>
    </form>
</div>
@endsection
