@extends('layouts.master')

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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filter By Status</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('projectFilter') }}" method="POST" id="projectCostForm">
                                @csrf
                                <div id="field-container">
                                    <div class="row mb-3 field-group">
                                        <div class="col-md-5">
                                            <select class="form-control" name="status" id="status">
                                                <option value="">All</option>
                                                <option value="1" {{ session('status') == '1' ? 'selected' : '' }}>Running</option>
                                                <option value="5" {{ session('status') == '5' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>
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
                <!-- Loop through each project, create a card for each -->
                @foreach($projects as $project)
                    <div class="col-md-6 col-lg-4">
                        <div class="card mb-4 wow fadeInUp" data-wow-duration="1500ms">
                        <div class="card-img-top">
                                <img src="{{ asset('uploads/projects/' . $project->details->image) }}"
                                    alt="{{ $project->details->title }}"
                                    class="img-fluid"
                                    style="width: 325; height: 200px; object-fit: cover;">
                            </div>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="icofont-location-pin"></i> {{ $project->details->location }}</li>
                                        <li><i class="fa-solid fa-bangladeshi-taka-sign"></i> BDT
                                            {{ $project->details->unit_price }}/Unit</li>
                                    </ul>
                                    <span class="badge
                                            @if($project->status == 1) bg-primary
                                            @elseif($project->status == 5) bg-danger
                                                @else bg-secondary
                                            @endif">
                                        @if($project->status == 1) Running
                                        @elseif($project->status == 5) Closed
                                            @else Unknown
                                        @endif
                                    </span>
                                </div>
                                <br>

                                <h4 class="card-title">
                                    <a
                                        href="{{ url('uploads/projects/' . $project->id) }}">{{ $project->details->title }}-{{ $project->unique_id }}</a>
                                </h4>
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

                                <a href="{{ route('project.edit', ['id' => $project->id]) }}" class="btn btn-primary">
                                    <i class="icofont-arrow-right"></i> Edit
                                </a>

                                <a href="{{ route('projectCosts', ['id' => $project->id]) }}" class="btn btn-primary"
                                    style="margin-top: 5px;">
                                    <i class="icofont-arrow-right"></i> Finance
                                </a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    @if ($projects->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $projects->previousPageUrl() }}" aria-label="Previous">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    @endif

                    @foreach ($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                        <li class="page-item {{ $projects->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    @if ($projects->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $projects->nextPageUrl() }}" aria-label="Next">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
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
                                <label class="form-label">Location Map URL</label>
                                <input type="text" class="form-control" name="location_map">
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
                                <label class="form-label">Youtube Video Link</label>
                                <input type="text" class="form-control" name="youtube_video" required>
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

        <script>
            document.getElementById("status").addEventListener("change", function () {
                document.getElementById("projectCostForm").submit();
        });
    </script>

@endsection

<style>
    .modal-lg {
        max-width: 900px;
        /* Adjust width */
    }
</style>
