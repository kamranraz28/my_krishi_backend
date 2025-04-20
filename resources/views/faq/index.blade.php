@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

<!-- Page Header Section -->
<section class="page-header text-center py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <h5 class="page-header__title display-4 font-weight-bold text-primary mb-3">
            {{ $project->details->title }} ({{ $project->unique_id }})
        </h5>
    </div>
</section>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex justify-content-between align-items-center" role="alert">
        <span>{{ session('success') }}</span>
        <button type="button" class="btn" data-bs-dismiss="alert" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between align-items-center" role="alert">
        <span>{{ session('error') }}</span>
        <button type="button" class="btn" data-bs-dismiss="alert" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<!-- FAQ Add Form Section -->
<section class="blog-one mb-5">
    <div class="container">
        <div class="row gutter-y-30">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add FAQ</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('storeFAQ') }}" method="POST">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <div id="field-container">
                                <div class="row mb-3 field-group">
                                    <div class="col-md-5">
                                        <input type="text" name="question[]" class="form-control" placeholder="Enter Question" required>
                                    </div>

                                    <div class="col-md-5">
                                        <input type="text" name="answer[]" class="form-control" placeholder="Enter Answer of the Question" required>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-field" style="display: none;">Remove</button>
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

        <!-- Existing FAQs List -->
        @if($project->faq && count($project->faq))
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Existing FAQs</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($project->faq as $faq)
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><strong>Q:</strong> {{ $faq->question }}</h6>
                                            <p class="mb-0"><strong>A:</strong> {{ $faq->answer }}</p>
                                        </div>
                                        <div class="btn-group">

                                            <a href="{{ route('editFAQ', $faq->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                                            <form action="{{ route('deleteFAQ', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- JavaScript for Dynamic Fields -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fieldContainer = document.getElementById('field-container');
        const addMoreBtn = document.getElementById('add-more');

        addMoreBtn.addEventListener('click', function () {
            let fieldGroup = document.createElement('div');
            fieldGroup.classList.add('row', 'mb-3', 'field-group');

            fieldGroup.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="question[]" class="form-control" placeholder="Enter Question" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="answer[]" class="form-control" placeholder="Enter Answer of the Question" required>
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
