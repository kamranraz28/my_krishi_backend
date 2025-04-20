@extends('layouts.master')

@section('title', 'Edit FAQ')

@section('content')

    <!-- Page Header -->
    <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h5 class="page-header__title display-4 font-weight-bold text-primary mb-3">
                Edit FAQ – {{ $faq->project->details->title }} ({{ $faq->project->unique_id }})
            </h5>
        </div>
    </section>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Edit FAQ Form -->
    <section class="faq-edit-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Add FAQ</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('updateFAQ', $faq->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="question" class="form-label">Question</label>
                                    <input type="text" class="form-control" id="question" name="question"
                                        value="{{ old('question', $faq->question) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="answer" class="form-label">Answer</label>
                                    <textarea class="form-control" id="answer" name="answer" rows="4"
                                        required>{{ old('answer', $faq->answer) }}</textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                                    <button type="submit" class="btn btn-success">Update FAQ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
