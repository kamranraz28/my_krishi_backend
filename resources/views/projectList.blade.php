@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Project List</h4>
        </div>
    </section>

    <div class="d-flex justify-content-end mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
            <i class="fas fa-plus"></i> Create Project
        </button>
    </div>

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
                <!-- Loop through each project, create a card for each -->
                @foreach($projects as $project)
                    <div class="col-md-6 col-lg-4">
                        <div class="card mb-4 wow fadeInUp" data-wow-duration="1500ms">
                            <div class="card-img-top">
                                <img src="{{ asset('uploads/projects/' . $project->details->image) }}"
                                    alt="{{ $project->details->title }}" class="img-fluid" style="height: 200px;">
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="icofont-location-pin"></i> {{ $project->details->location }}</li>
                                    <li><i class="fa-solid fa-bangladeshi-taka-sign"></i> BDT
                                        {{ $project->details->unit_price }}/Unit
                                    </li>
                                </ul>
                                <h3 class="card-title">
                                    <a href="{{ url('uploads/projects/' . $project->id) }}">{{ $project->details->title }}</a>
                                </h3>
                                <p class="card-text">
                                    Period: {{ $project->details->duration }} <br />
                                    Return: {{ $project->details->return_amount }}%
                                </p>
                                <a href="{{ route('projectUpdates', ['id' => $project->id]) }}" class="btn btn-primary">
                                    <i class="icofont-arrow-right"></i> Updates
                                </a>

                                <a href="{{ route('projectPeople', ['id' => $project->id]) }}" class="btn btn-primary">
                                    <i class="icofont-arrow-right"></i> People
                                </a>

                                <a href="{{ route('project.edit',['id'=>$project->id]) }}" class="btn btn-primary">
                                    <i class="icofont-arrow-right"></i> Edit
                                </a>

                                <a href="{{ route('projectCosts',['id'=>$project->id]) }}" class="btn btn-primary" style="margin-top: 5px;">
                                    <i class="icofont-arrow-right"></i> Finance
                                </a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Updated Modal -->
        <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Added modal-lg to make it wider -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalLabel">Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i> <!-- Font Awesome "X" Icon -->
                        </button>

                    </div>
                    <div class="modal-body">
                        <form action="{{ route('storeProject') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Price</label>
                                <input type="number" class="form-control" name="total_price" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Unit Price</label>
                                <input type="number" class="form-control" name="unit_price" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Units</label>
                                <input type="number" class="form-control" name="unit" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control" name="image">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Duration</label>
                                <input type="text" class="form-control" name="duration" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Return Amount (%)</label>
                                <input type="text" class="form-control" name="return_amount" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Project</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </section>

@endsection

<style>
    .modal-lg {
        max-width: 900px;
        /* Adjust width */
    }
</style>
