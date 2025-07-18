@extends('layouts.app')

@section('title', 'Internal Server Error - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <div class="error-template">
                <h1 class="display-1 text-danger">500</h1>
                <h2 class="mb-4">Internal Server Error</h2>
                <p class="lead mb-4">
                    Something went wrong on our end. We're working to fix it!
                </p>
                
                @if(config('app.debug') && isset($exception))
                <div class="error-details mt-4">
                    <div class="alert alert-danger text-start">
                        <h5><i class="bi bi-bug-fill me-2"></i>Error Details</h5>
                        <div class="mb-3">
                            <strong>Exception:</strong> {{ get_class($exception) }}
                        </div>
                        <div class="mb-3">
                            <strong>Message:</strong> {{ $exception->getMessage() }}
                        </div>
                        <div class="mb-3">
                            <strong>File:</strong> {{ $exception->getFile() }}
                        </div>
                        <div class="mb-3">
                            <strong>Line:</strong> {{ $exception->getLine() }}
                        </div>
                        @if($exception->getPrevious())
                        <div class="mb-3">
                            <strong>Previous:</strong> {{ $exception->getPrevious()->getMessage() }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="accordion" id="stackTraceAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stackTrace">
                                    <i class="bi bi-list-ul me-2"></i>Stack Trace
                                </button>
                            </h2>
                            <div id="stackTrace" class="accordion-collapse collapse" data-bs-parent="#stackTraceAccordion">
                                <div class="accordion-body text-start">
                                    <pre class="bg-dark text-light p-3 rounded" style="font-size: 0.8rem; overflow-x: auto;">{{ $exception->getTraceAsString() }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="error-actions">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-house me-2"></i>Go to Home page
                    </a>
                    @if(config('app.debug'))
                    <button onclick="location.reload()" class="btn btn-secondary btn-lg ms-2">
                        <i class="bi bi-arrow-clockwise me-2"></i>Retry
                    </button>
                    @endif
                </div>
                
                <div class="mt-5">
                    <h5>What happened?</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-exclamation-triangle text-warning"></i> A server error occurred while processing your request</li>
                        <li><i class="bi bi-tools text-primary"></i> Our team has been notified and is working on a fix</li>
                        <li><i class="bi bi-arrow-clockwise text-primary"></i> Try refreshing the page in a few moments</li>
                        <li><i class="bi bi-arrow-right text-primary"></i> If the problem persists, go back to the <a href="{{ url('/') }}">homepage</a></li>
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

.error-details {
    max-width: 100%;
    margin: 0 auto;
}

.error-details pre {
    max-height: 300px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
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
    
    .error-details pre {
        font-size: 0.7rem !important;
    }
}
</style>
@endsection