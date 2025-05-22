@extends('layouts.master')

@section('title', 'Terms & Conditions')

@section('content')
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Terms & Conditions</h2>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('terms.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Add Template
            </a>
        </div>

        @foreach ($terms as $term)
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="card-title text-primary">{{ $term->title }}</h4>
                        <div>
                            <input type="text" class="form-control d-inline-block me-2" id="url-{{ $term->id }}"
                                   value="{{ route('terms.show', $term->id) }}" readonly style="max-width: 300px;">
                            <button class="btn btn-outline-secondary btn-sm"
                                    onclick="copyToClipboard('url-{{ $term->id }}')">
                                Copy URL
                            </button>
                        </div>
                    </div>
                    <div class="card-text content-body">
                        {!! $term->content !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        function copyToClipboard(id) {
            const input = document.getElementById(id);
            input.select();
            input.setSelectionRange(0, 99999); // For mobile
            navigator.clipboard.writeText(input.value)
                .then(() => alert('URL copied to clipboard!'))
                .catch(err => alert('Failed to copy URL'));
        }
    </script>
@endsection

@section('styles')
    <style>
        .content-body p {
            margin-bottom: 1rem;
            line-height: 1.7;
        }
        .content-body ul {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .content-body li {
            margin-bottom: 0.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        input[readonly] {
            background-color: #f8f9fa;
            cursor: default;
        }
    </style>
@endsection
