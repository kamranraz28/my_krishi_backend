@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header Section -->
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
                    <h5 class="mb-3">Costs Till Today: <span class="text-muted">{{ $totalCost ?? 0 }}</span></h5>
                </div>

                <div class="border-start flex-fill py-3 px-4">
                    <h5 class="mb-1">Total Investor: {{ $uniqueTotalInvestors }}</h5>
                    <h5 class="mb-1">Total Agents: {{ $uniqeAgents }}</h5>
                </div>

            </div>
        </div>
    </section>

    <div class="d-flex justify-content-end mb-4">
        {{-- Start Project --}}
        @if ($project->status == 1)
            <a href="{{ route('projects.start', ['id' => $project->id]) }}" class="btn btn-primary me-2" onclick="return confirm('Are you sure you want to start this project?')">
                <i class="fas fa-play"></i> Start Project
            </a>
        @else
            <button type="button" class="btn btn-primary me-2" disabled>
                <i class="fas fa-play"></i> Start Project
            </button>
        @endif

        {{-- Close Project --}}
        @if ($project->status == 2)
            <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                <i class="fas fa-lock"></i> Close Project
            </button>
        @else
            <button type="button" class="btn btn-secondary me-2" disabled>
                <i class="fas fa-lock"></i> Close Project
            </button>
        @endif

        {{-- Financial Details --}}
        @if ($project->status == 5)
            <a href="{{ route('projects.financeDetails', ['id' => $project->id]) }}" class="btn btn-success">
                <i class="fas fa-file-invoice-dollar"></i> Financial Details
            </a>
        @else
            <a class="btn btn-success" style="pointer-events: none; opacity: 0.5;" tabindex="-1" aria-disabled="true">
                <i class="fas fa-file-invoice-dollar"></i> Financial Details
            </a>
        @endif
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
                            <h5 class="mb-0">Add Project Costs</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('projects.financeStore') }}" method="POST">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <div id="field-container">
                                    <div class="row mb-3 field-group">
                                        <div class="col-md-4">
                                            <input type="text" name="reason[]" class="form-control" placeholder="Cost Field"
                                                required>
                                        </div>

                                        <div class="col-md-3">
                                            <input type="number" name="cost[]" class="form-control" placeholder="Cost Value"
                                                required>
                                        </div>

                                        <div class="col-md-3">
                                            <input type="number" name="voucher[]" class="form-control"
                                                placeholder="Voucher Number" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-field"
                                                style="display: none;">Remove</button>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary" id="add-more">Add More</button>
                                <button type="submit" class="btn btn-success">Submit</button>
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Project Costs</h4>
                        </div>
                        <div class="card-body">
                            <table class="table" id="example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cost Field</th>
                                        <th>Cost Value</th>
                                        <th>Voucher Number</th>
                                        <th>Cost submitted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($costs as $key => $cost)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $cost->field ?? ''}}</td>
                                            <td>{{ $cost->cost ?? ''}}</td>
                                            <td>{{ $cost->voucher ?? 'N/A'}}</td>
                                            <td>{{ $cost->created_at }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <h5 class="mb-3">Total Cost: <span class="text-muted">{{ $totalCost ?? 0 }}</span></h5>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Added modal-lg to make it wider -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectModalLabel">Close Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i> <!-- Font Awesome "X" Icon -->
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('projects.close') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <div class="mb-3">
                                <label class="form-label">Total Recieved Value</label>
                                <input type="number" class="form-control" name="closing_amount"
                                    placeholder="Enter Recieved Value" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Service Charge (%)</label>
                                <input type="number" class="form-control" name="service_charge"
                                    placeholder="Enter Service Charge" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" name="remarks" placeholder="Enter Remarks" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload File</label>
                                <input type="file" class="form-control" name="voucher_file" placeholder="Upload File"
                                    >
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fieldContainer = document.getElementById('field-container');
            const addMoreBtn = document.getElementById('add-more');

            addMoreBtn.addEventListener('click', function () {
                let fieldGroup = document.createElement('div');
                fieldGroup.classList.add('row', 'mb-3', 'field-group');

                fieldGroup.innerHTML = `
                                        <div class="col-md-4">
                                            <input type="text" name="reason[]" class="form-control" placeholder="Cost Field" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="cost[]" class="form-control" placeholder="Cost Value" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="voucher[]" class="form-control" placeholder="Voucher Number"
                                                required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-field">Remove</button>
                                        </div>
                                    `;

                fieldContainer.appendChild(fieldGroup);

                updateRemoveButtons();
            });

            fieldContainer.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-field')) {
                    e.target.closest('.field-group').remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                let removeButtons = document.querySelectorAll('.remove-field');
                removeButtons.forEach((btn, index) => {
                    btn.style.display = index === 0 ? 'none' : 'block';
                });
            }

            updateRemoveButtons();
        });
    </script>

@endsection
