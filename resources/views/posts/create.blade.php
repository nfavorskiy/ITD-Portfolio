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

    <form method="POST" action="{{ route('posts.store') }}" id="create-post-form">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="title-feedback" class="small mt-1" style="display: none;"></div>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="5" required>{{ old('content') }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" id="submit-btn">Create Post</button>
        <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
    </form>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const titleFeedback = document.getElementById('title-feedback');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');

    // Title validation regex (matches server-side)
    const titleRegex = /^[a-zA-Z0-9\s\-\_\.\,\!\?\'\"\:\;]+$/;

    function validateTitle() {
        const title = titleInput.value.trim();
        
        titleInput.classList.remove('is-invalid', 'is-valid');
        titleFeedback.style.display = 'none';
        
        if (title.length === 0) {
            titleFeedback.textContent = 'Please enter a title.';
            titleFeedback.className = 'small mt-1 text-danger';
            titleFeedback.style.display = 'block';
            titleInput.classList.add('is-invalid');
            return false;
        }
        
        if (title.length > 255) {
            titleFeedback.textContent = 'Title must not exceed 255 characters.';
            titleFeedback.className = 'small mt-1 text-danger';
            titleFeedback.style.display = 'block';
            titleInput.classList.add('is-invalid');
            return false;
        }
        
        if (!titleRegex.test(title)) {
            titleFeedback.textContent = 'Title can only contain letters, numbers, spaces, and common punctuation (.,!?\'":-_).';
            titleFeedback.className = 'small mt-1 text-danger';
            titleFeedback.style.display = 'block';
            titleInput.classList.add('is-invalid');
            return false;
        }
        
        titleFeedback.textContent = '✓ Title is valid';
        titleFeedback.className = 'small mt-1 text-success';
        titleFeedback.style.display = 'block';
        titleInput.classList.add('is-valid');
        return true;
    }

    titleInput.addEventListener('input', validateTitle);
    titleInput.addEventListener('blur', validateTitle);

    document.getElementById('create-post-form').addEventListener('submit', function(e) {
        if (!validateTitle()) {
            e.preventDefault();
            titleInput.focus();
        }
    });

    cancelBtn.addEventListener('click', function() {
        history.back();
    });
});
</script>
@endsection