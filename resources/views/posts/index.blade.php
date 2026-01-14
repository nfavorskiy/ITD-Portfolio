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
        @can('create', App\Models\Post::class)
            <a href="{{ route('posts.create') }}" class="btn btn-primary">Add Post</a>
        @endcan
    </div>
    <div class="scroll-content">
        <ul id="posts-list" class="list-group list-group-flush">
            <li id="loading-indicator" class="list-group-item text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 mb-0 text-muted">Loading posts...</p>
            </li>
        </ul>

        <nav id="pagination-container" class="mt-4 d-none" aria-label="Posts pagination">
            <ul class="pagination justify-content-center mb-0"></ul>
            <p id="pagination-info" class="text-center text-muted mt-2 small"></p>
        </nav>
    </div>
</div>

<style nonce="{{ $cspNonce }}">
.post-item:hover {
    background-color: #d2d3d4ff !important;
    cursor: pointer;
}

.post-title {
    font-size: 1.2rem;
}

.post-content-preview {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 4.5em;
    line-height: 1.5em;
    margin-bottom: 0.5rem;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.pagination .page-link {
    cursor: pointer;
    color: #000;
    border-width: 2px;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    border-width: 2px;
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    cursor: not-allowed;
    color: #6c757d;
    border-width: 2px;
}

.pagination .page-link:hover {
    border-color: #0d6efd;
}
</style>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const perPage = 10;
    let currentPage = 1;
    let currentUserId = @json(auth()->check() ? auth()->user()->id : null);
    let isCurrentUserModerator = @json(auth()->check() ? auth()->user()->isAdmin() : false);
    const csrfToken = '{{ csrf_token() }}';
    const postsBaseUrl = '{{ url('posts') }}';

    function getQueryParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }

    function updateUrlWithPage(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.history.replaceState({}, '', url.toString());
    }

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
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    });

    function buildApiUrl(page) {
        let apiUrl = '{{ route('posts.api') }}?page=' + page + '&per_page=' + perPage;
        if (getQueryParam('mine') === '1') {
            apiUrl += '&mine=1';
        }
        return apiUrl;
    }

    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        const ul = container.querySelector('.pagination');
        const info = document.getElementById('pagination-info');
        ul.innerHTML = '';

        if (data.last_page <= 1) {
            container.classList.add('d-none');
            return;
        }

        container.classList.remove('d-none');
        info.textContent = `Showing ${data.from}-${data.to} of ${data.total} posts`;

        ul.innerHTML += `
            <li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" data-page="${data.current_page - 1}" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        `;

        let startPage = Math.max(1, data.current_page - 2);
        let endPage = Math.min(data.last_page, data.current_page + 2);

        if (startPage > 1) {
            ul.innerHTML += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
            if (startPage > 2) {
                ul.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            ul.innerHTML += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < data.last_page) {
            if (endPage < data.last_page - 1) {
                ul.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            ul.innerHTML += `<li class="page-item"><a class="page-link" data-page="${data.last_page}">${data.last_page}</a></li>`;
        }

        ul.innerHTML += `
            <li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
                <a class="page-link" data-page="${data.current_page + 1}" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;

        ul.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page >= 1 && page <= data.last_page && page !== data.current_page) {
                    loadPosts(page);
                }
            });
        });
    }

    // Policy-based permission check (mirrors Laravel Policy logic)
    function canUpdate(post) {
        return currentUserId && currentUserId === post.user_id;
    }

    function canDelete(post) {
        return currentUserId && (currentUserId === post.user_id || isCurrentUserModerator);
    }

    function renderPost(post) {
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

        let createdAt = new Date(post.created_at).toLocaleString('en-US', {year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true});
        let updatedAt = new Date(post.updated_at).toLocaleString('en-US', {year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true});

        return `
        <li class="list-group-item d-flex justify-content-between align-items-center post-item mb-3" 
            data-post-url="${postsBaseUrl}/${post.id}"
            style="background-color: #e9ecef; transition: background-color 0.2s ease; border-radius: 0.375rem;">
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
                ${canUpdate(post) ? `<a href="${postsBaseUrl}/${post.id}/edit" class="btn btn-sm btn-primary post-edit-btn" style="min-width: 60px; background-color: white; border-color: #007bff; color: #007bff;">Edit</a>` : ''}
                ${canDelete(post) ? `
                <form method="POST" action="${postsBaseUrl}/${post.id}" class="post-delete-form">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger post-delete-btn" style="min-width: 60px; background-color: white; border-color: #dc3545; color: #dc3545;">Delete</button>
                </form>` : ''}
            </div>
        </li>
        `;
    }

    function attachPostEventListeners() {
        document.querySelectorAll('.post-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (e.target.closest('.post-edit-btn') || e.target.closest('.post-delete-btn') || e.target.closest('.post-delete-form')) {
                    return;
                }
                window.location.href = this.dataset.postUrl;
            });
        });

        document.querySelectorAll('.post-edit-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        document.querySelectorAll('.post-delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.stopPropagation();
                if (!confirm('Delete this post?')) {
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.post-delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    }

    function loadPosts(page = 1) {
        currentPage = page;
        updateUrlWithPage(page);

        const postsList = document.getElementById('posts-list');
        
        postsList.innerHTML = `
            <li class="list-group-item text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 mb-0 text-muted">Loading posts...</p>
            </li>
        `;

        fetch(buildApiUrl(page))
            .then(response => response.json())
            .then(response => {
                postsList.innerHTML = '';

                const posts = response.data;

                if (!posts.length) {
                    postsList.innerHTML = '<li class="list-group-item text-center text-muted">No Posts found</li>';
                    document.getElementById('pagination-container').classList.add('d-none');
                    return;
                }

                posts.forEach((post, i) => {
                    setTimeout(() => {
                        postsList.insertAdjacentHTML('beforeend', renderPost(post));
                        if (i === posts.length - 1) {
                            attachPostEventListeners();
                        }
                    }, i * 50);
                });

                renderPagination(response);

                if (page > 1) {
                    document.getElementById('posts-list').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(() => {
                postsList.innerHTML = '<li class="list-group-item text-center text-danger">Failed to load posts. Please try again.</li>';
            });
    }

    const initialPage = parseInt(getQueryParam('page')) || 1;
    loadPosts(initialPage);
});
</script>
@endsection