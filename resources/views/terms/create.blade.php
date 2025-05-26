@extends('layouts.master')

@section('title', 'TinyMCE Test')

@section('content')
<div class="container mt-4">
    <h1>Add New Template</h1>

    <form method="POST" action="{{ route('conditions.store') }}">
        {{-- CSRF Token --}}
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection

@section('scripts')
    {{-- TinyMCE CDN --}}
<script src="https://cdn.tiny.cloud/1/1y9vx4o30jq411q3j85cn4dv1dujvqtpotwnqtmzi49dxp3l/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    {{-- TinyMCE Init Script --}}
    <script>
        tinymce.init({
            selector: 'textarea#content',
            plugins: 'lists link image code',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
            menubar: false,
            height: 300
        });
    </script>
@endsection
