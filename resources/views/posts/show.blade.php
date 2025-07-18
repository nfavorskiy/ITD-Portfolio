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
                @if(auth()->check())
                    {{-- Only post author can edit --}}
                    @if(auth()->user()->id === $post->user_id)
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-primary">Edit</a>
                    @endif
                    
                    {{-- Post author OR admin can delete --}}
                    @if(auth()->user()->id === $post->user_id || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</button>
                        </form>
                    @endif
                @endif
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
                        <strong class="{{ $authorClass }}" style="{{ $authorStyle }}">{!! e($authorName) . $youLabel !!}</strong>
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
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Posts
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Convert all timestamps to user's local timezone
    const timeElements = document.querySelectorAll('.local-time');
    
    timeElements.forEach(function(element) {
        const utcTime = element.getAttribute('data-utc');
        const localDate = new Date(utcTime);
        
        // Format the date in user's local timezone
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        };
        
        const localTimeString = localDate.toLocaleString('en-US', options);
        element.textContent = localTimeString;
    });
});
</script>
@endsection