<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS (if not already included elsewhere) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom styles for thicker form borders -->
    <style>
        .form-control {
            border-width: 2px !important;
            border-color: #6c757d !important;
        }
        .form-control:focus {
            border-width: 2px !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
        .form-check-input {
            border-width: 2px !important;
            border-color: #6c757d !important;
        }
        .form-check-input:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="py-4">
        {{ $slot }}
    </main>
</body>
</html>