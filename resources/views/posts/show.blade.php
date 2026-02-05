@php
    $currentUserId = auth()->check() ? auth()->user()->id : null;
    $authorIsModerator = $post->author_is_admin ?? false;
    $authorName = $post->author_name ?? 'Unknown';
    $authorClass = 'text-dark fw-bold';
    $youLabel = '';

    if ($currentUserId && $currentUserId === $post->user_id) {
        $authorClass = 'bg-success text-white px-2 py-1 rounded';
        $youLabel = ' (You)';
    } elseif ($authorIsModerator && $currentUserId !== $post->user_id) {
        $authorClass = 'bg-warning text-dark px-2 py-1 rounded';
        $youLabel = ' (Moderator)';
    } elseif ($currentUserId && ! $authorIsModerator && $authorName !== 'Deleted User') {
        $authorClass = 'px-2 py-1 rounded" style="background-color: #000dfdff; color: #fff;';
        $youLabel = ' (Beginner)';
    }

    $authorBreadcrumb = '<strong class="' . $authorClass . '">' . e($authorName) . $youLabel . '</strong>';
    $links = [
        'Posts' => route('posts.index'),
        $post->title . ' <em>by</em> ' . $authorBreadcrumb => '',
    ];
@endphp

@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="mb-0">{{ $post->title }}</h1>
            <div class="d-flex gap-2">
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-primary">Edit</a>
                @endcan
                
                @can('delete', $post)
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="small text-secondary">
                    <span class="me-3">
                        <i class="bi bi-person me-1"></i>
                        By: 
                        @php
                            $authorClass = 'text-dark fw-bold';
                            $youLabel = '';
                            $authorStyle = '';
                            if ($currentUserId && $currentUserId === $post->user_id) {
                                $authorClass = 'bg-success text-white px-2 py-1 rounded';
                                $youLabel = ' (You)';
                            } elseif ($authorIsModerator && $currentUserId !== $post->user_id) {
                                $authorClass = 'bg-warning text-dark px-2 py-1 rounded';
                                $youLabel = ' (Moderator)';
                            } elseif ($currentUserId && ! $authorIsModerator && $authorName !== 'Deleted User') {
                                $authorClass = 'px-2 py-1 rounded';
                                $authorStyle = 'background-color: #000dfdff; color: #fff;';
                                $youLabel = ' (Beginner)';
                            }
                        @endphp
                        <strong class="{{ $authorClass }}" style="{{ $authorStyle }}">{{ $authorName . $youLabel }}</strong>
                    </span>
                    <span class="me-3">
                        <i class="bi bi-calendar-plus me-1"></i>
                        Created: <span class="local-time" data-utc="{{ $post->created_at->toISOString() }}">{{ $post->created_at->format('M j, Y g:i A') }}</span>
                    </span>
                    @if($post->created_at != $post->updated_at)
                        <span>
                            <i class="bi bi-pencil me-1"></i>
                            Edited: <span class="local-time" data-utc="{{ $post->updated_at->toISOString() }}">{{ $post->updated_at->format('M j, Y g:i A') }}</span>
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="post-content">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary back-btn">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Posts
            </button>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    // Back button
    document.querySelector('.back-btn').addEventListener('click', function() {
        history.back();
    });

    // Delete form confirmation
    const deleteForm = document.querySelector('.delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            if (!confirm('Delete this post?')) {
                e.preventDefault();
            } else {
                window.history.replaceState(null, '', '{{ route('posts.index') }}');
            }
        });
    }

    // Convert UTC times to local
    const timeElements = document.querySelectorAll('.local-time');
    timeElements.forEach(function(element) {
        const utcTime = element.getAttribute('data-utc');
        const localDate = new Date(utcTime);
        
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        };
        
        element.textContent = localDate.toLocaleString('en-US', options);
    });
});
</script>
@endsection