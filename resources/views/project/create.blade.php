@extends('layouts.master')

@section('content')

<!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Add New Project</h4>
        </div>
    </section>

<section class="blog-one">
    <div class="container">
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Price</label>
                            <input type="number" class="form-control" name="total_price" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Unit Price</label>
                            <input type="number" class="form-control" name="unit_price" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Units</label>
                            <input type="number" class="form-control" name="unit" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location Map URL</label>
                            <input type="text" class="form-control" name="location_map">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Youtube Video Link</label>
                            <input type="text" class="form-control" name="youtube_video" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Duration</label>
                            <input type="text" class="form-control" name="duration" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Return Amount (%)</label>
                            <input type="text" class="form-control" name="return_amount" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Terms and Conditions URL</label>
                            <input type="text" class="form-control" name="terms_url">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Project</button>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
