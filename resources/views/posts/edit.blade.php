@php
    $authorName = $post->author_name ?? 'Unknown';
    $authorClass = 'bg-success fw-bold text-white px-2 py-1 rounded';
    $youLabel = ' (You)';

    $authorBreadcrumb = '<strong class="' . $authorClass . '">' . e($authorName) . $youLabel . '</strong>';

    $links = [
        'Posts' => route('posts.index'),
        $post->title . ' <em>by</em> ' .  $authorBreadcrumb => '',
    ];
@endphp

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Post</h1>

    <form method="POST" action="{{ route('posts.update', $post) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" value="{{ $post->title }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control" rows="5">{{ $post->content }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
        <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
    </form>
</div>
<script nonce="{{ $cspNonce }}">
    document.getElementById('cancel-btn').addEventListener('click', function() {
        history.back();
    });
</script>
@endsection
