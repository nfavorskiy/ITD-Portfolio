@php
    $links = [
        'Posts' => route('posts.index'),
        'New Post' => '',
    ];
@endphp

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Create Post</h1>

    <form method="POST" action="{{ route('posts.store') }}">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control" rows="5"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
    </form>
</div>
@endsection
