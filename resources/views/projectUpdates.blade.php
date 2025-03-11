@extends('layouts.master')

@section('title', 'Project Updates')

@section('content')
    <section class="project-updates">
        <div class="container">
            <!-- Page Header Section -->
            <section class="page-header text-center py-5" style="background-color: #f8f9fa;">
                <div class="container">
                    <h4 class="page-header__title display-4 font-weight-bold text-primary mb-3">{{ $project->details->title }}</h4>
                    <h5 class="mb-3">Total Units: <span class="text-muted">{{ $project->details->unit }}</span></h5>
                    <h5 class="mb-3">Total Price: <span class="text-muted">{{ $project->details->total_price }}</span></h5>
                </div>
            </section>

            @php
                $levels = [
                    100 => 'Admin',
                    200 => 'Investor',
                    300 => 'Agent'
                ];
            @endphp

            @foreach($projectUpdates as $update)
                <div class="post shadow-sm rounded">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="User" class="avatar">
                            <div>
                                <span class="username">
                                    {{ $update->user->name ?? 'Anonymous' }}
                                    <span class="badge badge-primary">
                                        {{ $levels[$update->user->level] ?? 'Member' }}
                                    </span>
                                </span>
                                <span class="post-time">{{ $update->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="post-body">
                        <p>{{ $update->description }}</p>
                        @foreach($update->image_urls as $image)
                            <img src="{{ $image }}" alt="Update Image" class="post-image">
                        @endforeach
                    </div>

                    <div class="post-comments">
                        @foreach($update->comment as $comment)
                            <div class="comment">
                                <div class="comment-user">
                                    <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="User" class="avatar">
                                    <div>
                                        <span class="username">
                                            {{ $comment->user->name ?? 'Anonymous' }}
                                            <span class="badge badge-secondary">
                                                {{ $levels[$comment->user->level] ?? 'Member' }}
                                            </span>
                                        </span>
                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <p class="comment-text">{{ $comment->comment }}</p>
                                @foreach($comment->reply as $reply)
                                    <div class="reply">
                                        <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="User" class="avatar">
                                        <div>
                                            <span class="username">
                                                {{ $reply->user->name ?? 'Anonymous' }}
                                                <span class="badge badge-info">
                                                    {{ $levels[$reply->user->level] ?? 'Member' }}
                                                </span>
                                            </span>
                                            <p class="reply-text">{{ $reply->reply }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="post-comment-form">
                        <form action="{{ route('comment', ['id' => $update->id]) }}" method="POST">
                            @csrf
                            <div class="comment-box">
                                <input type="text" name="comment" placeholder="Write a comment..." class="comment-input">
                                <button type="submit" class="btn-submit">Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
@endsection

<style>
    .container {
        max-width: 600px;
        margin: auto;
    }

    .post {
        background-color: #fff;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .post-header {
        display: flex;
        align-items: center;
    }

    .post-user {
        display: flex;
        align-items: center;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .post-time,
    .comment-time {
        font-size: 0.9rem;
        color: #888;
    }

    .post-image {
        max-width: 25%;
        border-radius: 8px;
        margin-top: 10px;
    }

    .post-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

    .post-comments {
        margin-top: 15px;
    }

    .comment {
        background: #f0f2f5;
        padding: 8px;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .comment-user {
        display: flex;
        align-items: center;
    }

    .comment-text {
        margin-top: 5px;
        font-size: 14px;
    }

    .reply {
        display: flex;
        margin-left: 40px;
        margin-top: 5px;
    }

    .reply-text {
        font-size: 13px;
        color: #555;
    }

    .comment-input {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 20px;
        background: #f0f2f5;
    }

    .post-comment-form {
        margin-top: 10px;
    }

    .comment-box {
        display: flex;
        align-items: center;
        background: #f0f2f5;
        padding: 8px 12px;
        border-radius: 50px;
        border: 1px solid #ddd;
    }

    .comment-input {
        flex-grow: 1;
        border: none;
        outline: none;
        padding: 8px 12px;
        background: transparent;
        font-size: 14px;
    }

    .btn-submit {
        background-color: #1877f2;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background-color: #145dbf;
    }

    .badge {
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 12px;
        margin-left: 5px;
        color: white;
    }

    .badge-primary {
        background-color: #007bff;
    }

    .badge-secondary {
        background-color: #6c757d;
    }

    .badge-info {
        background-color: #17a2b8;
    }
</style>
