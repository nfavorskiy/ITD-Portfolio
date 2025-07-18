@extends('layouts.app')

@section('title', 'Page Not Found - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1 text-muted">404</h1>
                <h2 class="mb-4">Page Not Found</h2>
                <p class="lead mb-4">
                    Sorry, the page you are looking for could not be found.
                </p>
                <div class="error-actions">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-house me-2"></i>Go to Home page
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Go Back
                    </button>
                </div>

                <div class="mt-5">
                    <h5>What happened?</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-link-45deg text-warning"></i> The page you requested doesn't exist or has been moved</li>
                        <li><i class="bi bi-keyboard text-primary"></i> You may have typed the URL incorrectly</li>
                        <li><i class="bi bi-bookmark-x text-primary"></i> The link you followed might be broken or outdated</li>
                        <li><i class="bi bi-arrow-right text-primary"></i> Try going back to the <a href="{{ url('/') }}">homepage</a> or to the <button onclick="history.back()" class="btn-link p-0 text-primary" style="border: none; background: none; text-decoration: none;">previous page</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-template {
    padding: 40px 15px;
    text-align: center;
}

.error-template h1 {
    font-size: 8rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.error-template h2 {
    color: #333;
    font-weight: 600;
}

.error-template .lead {
    color: #666;
    font-size: 1.1rem;
}

.error-actions {
    margin-top: 30px;
}

.error-template ul li {
    margin: 10px 0;
    color: #666;
}

.error-template ul li a {
    color: #0d6efd;
    text-decoration: none;
}

.error-template ul li a:hover {
    text-decoration: underline;
}

.error-template ul li button:hover {
    text-decoration: underline !important;
}

@media (max-width: 768px) {
    .error-template h1 {
        font-size: 4rem;
    }
    
    .error-actions .btn {
        display: block;
        width: 100%;
        margin: 10px 0;
    }
}
</style>
@endsection