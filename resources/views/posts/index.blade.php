@extends('layouts.app')

@section('title', 'Posts - ' . config('app.name'))

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 id="posts-heading" class="mb-0">Posts</h1>
        <div class="d-flex align-items-center ms-auto me-3">
            <input type="checkbox" id="mine-checkbox" class="form-check-input me-2">
            <label for="mine-checkbox" class="form-check-label" style="user-select:none;cursor:pointer;">Show <strong>only</strong> your Posts</label>
        </div>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Add Post</a>
    </div>
    <ul id="posts-list" class="list-group list-group-flush">
        <li id="loading-indicator" class="list-group-item text-center">Loading posts...</li>
    </ul>
</div>

<style>
.post-item:hover {
    background-color: #d2d3d4ff !important;
    cursor: pointer;
}

.post-title {
    font-size: 1.2rem; /* Larger font size for post titles */
}

/* Limit post content to 3 lines with ellipsis */
.post-content-preview {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 4.5em; /* Approx. 3 lines */
    line-height: 1.5em;
    margin-bottom: 0.5rem;
}
</style>

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

    // Helper to get query params from current URL
    function getQueryParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }

    // Checkbox logic
    const mineCheckbox = document.getElementById('mine-checkbox');
    if (getQueryParam('mine') === '1') {
        mineCheckbox.checked = true;
        document.getElementById('posts-heading').textContent = 'Your Posts';
    }

    mineCheckbox.addEventListener('change', function() {
        const url = new URL(window.location.href);
        if (mineCheckbox.checked) {
            url.searchParams.set('mine', '1');
        } else {
            url.searchParams.delete('mine');
        }
        window.location.href = url.toString();
    });

    // Build API URL with ?mine=1 if present in page URL
    let apiUrl = '{{ route('posts.api') }}';
    if (getQueryParam('mine') === '1') {
        apiUrl += '?mine=1';
    }

    // Fetch posts via AJAX and render them one by one
    fetch(apiUrl)
        .then(response => response.json())
        .then(posts => {
            const postsList = document.getElementById('posts-list');
            postsList.innerHTML = ''; // Clear loading indicator

            if (!posts.length) {
                postsList.innerHTML = '<li class="list-group-item text-center text-muted">No Posts found</li>';
                return;
            }

            function renderPost(post, index) {
                // Build post HTML (simplified for brevity)
                let currentUserId = @json(auth()->check() ? auth()->user()->id : null);
                let authorIsModerator = post.author_is_admin ?? false;

                let authorName = post.author_name ?? 'Unknown';
                let youLabel = '';
                let authorClass = 'text-dark fw-bold';

                if (currentUserId && currentUserId === post.user_id) {
                    authorClass = 'bg-success text-white px-2 py-1 rounded';
                    youLabel = ' (You)';
                } else if (authorIsModerator && currentUserId !== post.user_id) {
                    authorClass = 'bg-warning text-dark px-2 py-1 rounded';
                    youLabel = ' (Moderator)';
                } else if (currentUserId && !authorIsModerator && authorName !== 'Deleted User') {
                    authorClass = 'px-2 py-1 rounded" style="background-color: #000dfdff; color: #fff;';
                    youLabel = ' (Beginner)';
                }

                let canEdit = (currentUserId && currentUserId === post.user_id);

                // Only moderators or the post owner can delete
                let isCurrentUserModerator = @json(auth()->check() ? auth()->user()->isAdmin() : false);
                let canDelete = (currentUserId && (currentUserId === post.user_id || isCurrentUserModerator));

                let createdAt = new Date(post.created_at).toLocaleString('en-US', {year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true});
                let updatedAt = new Date(post.updated_at).toLocaleString('en-US', {year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true});

                let postHtml = `
                <li class="list-group-item d-flex justify-content-between align-items-center post-item mb-3" style="background-color: #e9ecef; transition: background-color 0.2s ease; border-radius: 0.375rem;" onclick="window.location.href='{{ url('posts') }}/${post.id}'">
                    <div class="flex-grow-1">
                        <strong class="d-block mb-2 text-dark post-title">${post.title}</strong>
                        <p class="text-muted mb-2 post-content-preview">${post.content}</p>
                        <div class="small text-secondary">
                            <span class="me-3">
                                <i class="bi bi-person me-1"></i>
                                By: <strong class="${authorClass}">${authorName}${youLabel}</strong>
                            </span>
                            <span class="me-3">
                                <i class="bi bi-calendar-plus me-1"></i>
                                Created: <span>${createdAt}</span>
                            </span>
                            ${post.created_at !== post.updated_at ? `
                            <span>
                                <i class="bi bi-pencil me-1"></i>
                                Edited: <span>${updatedAt}</span>
                            </span>` : ''}
                        </div>
                    </div>
                    <div class="ms-3 d-flex flex-column gap-2">
                        ${canEdit ? `<a href="{{ url('posts') }}/${post.id}/edit" class="btn btn-sm btn-primary" style="min-width: 60px; background-color: white; border-color: #007bff; color: #007bff;" onclick="event.stopPropagation();">Edit</a>` : ''}
                        ${canDelete ? `
                        <form method="POST" action="{{ url('posts') }}/${post.id}" onsubmit="event.stopPropagation(); return confirm('Delete this post?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" style="min-width: 60px; background-color: white; border-color: #dc3545; color: #dc3545;" onclick="event.stopPropagation();">Delete</button>
                        </form>` : ''}
                    </div>
                </li>
                `;
                postsList.insertAdjacentHTML('beforeend', postHtml);
            }

            // Render posts one by one with a small delay for effect
            posts.forEach((post, i) => {
                setTimeout(() => renderPost(post, i), i); // 100ms delay between each
            });
        });
});
</script>
@endsection