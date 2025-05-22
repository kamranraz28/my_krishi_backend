@extends('layouts.master')

@section('title', 'Project Details')

@section('content')

    <!-- Page Header -->
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




    <!-- Project Update Form -->
    <section class="pb-5">
        <div class="container">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Update Project Details</h5>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="{{ route('projects.update', ['project' => $project->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Project Title</label>
                                <input type="text" class="form-control" name="title"
                                    value="{{ $project->details->title ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label for="total_price" class="form-label">Total Price</label>
                                <input type="text" class="form-control" name="total_price"
                                    value="{{ $project->details->total_price ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label for="unit_price" class="form-label">Unit Price</label>
                                <input type="text" class="form-control" name="unit_price"
                                    value="{{ $project->details->unit_price ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <input type="text" class="form-control" name="unit"
                                    value="{{ $project->details->unit ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" name="location"
                                    value="{{ $project->details->location ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label for="location_map" class="form-label">Location Map URL</label>
                                <input type="text" class="form-control" name="location_map"
                                    value="{{ $project->details->location_map ?? '' }}">
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description"
                                    rows="3">{{ $project->details->description ?? '' }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label for="image" class="form-label">Project Image</label>
                                <div class="mb-2">
                                    <img src="{{ asset('uploads/projects/' . $project->details->image) }}"
                                        class="img-thumbnail w-50" alt="Project Image">
                                </div>
                                <input class="form-control" type="file" name="image">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">YouTube Video Link</label>
                                <input type="text" class="form-control" name="youtube_video"
                                    value="{{ $project->details->youtube_video ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label for="duration" class="form-label">Duration</label>
                                <textarea class="form-control" name="duration"
                                    rows="3">{{ $project->details->duration ?? '' }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="return_amount" class="form-label">Return Amount</label>
                                <textarea class="form-control" name="return_amount"
                                    rows="3">{{ $project->details->return_amount ?? '' }}</textarea>
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
