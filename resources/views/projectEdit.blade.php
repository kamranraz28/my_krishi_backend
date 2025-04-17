@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">{{ $project->details->title }}</h4>
            <h5 class="mb-3">Total Units: <span class="text-muted">{{ $project->details->unit }}</span></h5>
            <h5 class="mb-3">Total Price: <span class="text-muted">{{ $project->details->total_price }}</span></h5>
        </div>
    </section>


    <!-- Projects Section -->
    <section class="blog-one">
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('projects.update', $project->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ $project->details->title ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Total Price</label>
                                    <input type="text" class="form-control" id="total_price" name="total_price"
                                        value="{{ $project->details->total_price ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Unit Price</label>
                                    <input type="text" class="form-control" id="unit_price" name="unit_price"
                                        value="{{ $project->details->unit_price ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Unit</label>
                                    <input type="text" class="form-control" id="unit" name="unit"
                                        value="{{ $project->details->unit ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        value="{{ $project->details->location ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location Map URL</label>
                                    <input type="text" class="form-control" id="location_map" name="location_map"
                                        value="{{ $project->details->location_map ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description"
                                        rows="3">{{ $project->details->description ?? '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    <br>
                                    <img src="{{ asset('uploads/projects/' . $project->details->image) }}" class="img-fluid w-50 rounded border" alt="Project Image">
                                    <input class="form-control mt-2" type="file" id="image" name="image">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Youtube Video Link</label>
                                    <input type="text" class="form-control" name="youtube_video" value="{{ $project->details->youtube_video ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration</label>
                                    <textarea class="form-control" id="duration" name="duration"
                                        rows="3">{{ $project->details->duration ?? '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="return_amount" class="form-label">Return Amount</label>
                                    <textarea class="form-control" id="return_amount" name="return_amount"
                                        rows="3">{{ $project->details->return_amount ?? '' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Project</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </section>

@endsection

