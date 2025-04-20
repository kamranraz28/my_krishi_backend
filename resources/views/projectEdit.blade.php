@extends('layouts.master')

@section('title', 'Project Details')

@section('content')

<!-- Page Header -->
<section class="page-header text-center py-5" style="background: linear-gradient(135deg, #e3f2fd, #ffffff);">
    <div class="container">
        <h2 class="display-6 fw-bold text-primary mb-2">{{ $project->details->title }}</h2>
        <p class="mb-1 text-muted">Project ID: {{ $project->unique_id }}</p>
        <p class="mb-1"><strong>Total Units:</strong> <span class="text-secondary">{{ $project->details->unit }}</span></p>
        <p class="mb-1"><strong>Total Price:</strong> <span class="text-secondary">{{ $project->details->total_price }}</span></p>
    </div>
</section>

<!-- Add FAQ Button -->
<section class="mb-4">
    <div class="container">
        <div class="d-flex justify-content-end">
            <a href="{{ route('addFAQ', $project->id) }}" class="btn btn-outline-info">
                <i class="fas fa-plus-circle me-1"></i> Add FAQ
            </a>
        </div>
    </div>
</section>

<!-- Project Update Form -->
<section class="pb-5">
    <div class="container">
        <div class="card border-0 shadow rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Update Project Details</h5>
            </div>
            <div class="card-body px-4 py-4">
                <form action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Project Title</label>
                            <input type="text" class="form-control" name="title" value="{{ $project->details->title ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="total_price" class="form-label">Total Price</label>
                            <input type="text" class="form-control" name="total_price" value="{{ $project->details->total_price ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="unit_price" class="form-label">Unit Price</label>
                            <input type="text" class="form-control" name="unit_price" value="{{ $project->details->unit_price ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" name="unit" value="{{ $project->details->unit ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" value="{{ $project->details->location ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="location_map" class="form-label">Location Map URL</label>
                            <input type="text" class="form-control" name="location_map" value="{{ $project->details->location_map ?? '' }}">
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3">{{ $project->details->description ?? '' }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="image" class="form-label">Project Image</label>
                            <div class="mb-2">
                                <img src="{{ asset('uploads/projects/' . $project->details->image) }}" class="img-thumbnail w-50" alt="Project Image">
                            </div>
                            <input class="form-control" type="file" name="image">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">YouTube Video Link</label>
                            <input type="text" class="form-control" name="youtube_video" value="{{ $project->details->youtube_video ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="duration" class="form-label">Duration</label>
                            <textarea class="form-control" name="duration" rows="3">{{ $project->details->duration ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="return_amount" class="form-label">Return Amount</label>
                            <textarea class="form-control" name="return_amount" rows="3">{{ $project->details->return_amount ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save me-1"></i> Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
