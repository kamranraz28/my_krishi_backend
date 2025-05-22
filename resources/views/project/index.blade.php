@extends('layouts.master')

@section('content')

    <!-- Page Header Section -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">Project List</h4>
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
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                Add Project
                            </a>
                        </div>

                        <!-- Card Body: Filter Form -->
                        <div class="card-body">
                            <form action="{{ route('projects.filter') }}" method="POST" id="projectCostForm">
                                @csrf
                                <div id="field-container">
                                    <div class="row mb-3 field-group">
                                        <div class="col-md-5">
                                            <select class="form-control" name="status" id="status">
                                                <option value="">All</option>
                                                <option value="1" {{ session('status') == '1' ? 'selected' : '' }}>Initiated</option>
                                                <option value="2" {{ session('status') == '2' ? 'selected' : '' }}>Running</option>
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
                                        @if($project->status == 1) Initiated
                                            @elseif($project->status == 2) Running
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

                                <a href="{{ route('projects.people', ['id' => $project->id]) }}" class="btn btn-primary">
                                    <i class="icofont-arrow-right"></i> People
                                </a>

                                <a href="{{ route('projects.edit', ['project' => $project->id]) }}" class="btn btn-primary">
                                    <i class="icofont-arrow-right"></i> Edit
                                </a>


                                <a href="{{ route('projects.finance', ['id' => $project->id]) }}" class="btn btn-primary"
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
