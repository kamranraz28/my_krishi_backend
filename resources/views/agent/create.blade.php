@extends('layouts.master')

@section('content')

    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Add New Agent</h4>
        </div>
    </section>

    <section class="blog-one">
        <div class="container">
            <div class="card mb-4">

                <div class="card-body">
                    <form action="{{ route('agents.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Agent Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Agent Name"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Agent Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone"
                                    placeholder="Enter Agent Phone" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Agent Email</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="Enter Agent Email" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Confirm Agent</button>
                            <a href="{{ route('agents.index') }}" class="btn btn-secondary">Back to Agent List</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
