@extends('layouts.app')

@section('title', Auth::user()->name . ' - Profile - ' . config('app.name'))

@section('content')
    <div class="container">
        <h2 class="mb-4 fw-bold" style="font-size:2.5rem;">
            Your Profile &nbsp; - &nbsp; {{ Auth::user()->name }}
        </h2>
        <div class="row g-4">
            <!-- Your Posts Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('posts.index', ['mine' => 1]) }}" class="profile-card-link">
                    <div class="profile-card h-100 d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="mb-4">
                            <!-- Modern Pulse/Activity Icon -->
                            <svg width="64" height="64" fill="none" viewBox="0 0 24 24">
                                <polyline points="3 12 7 12 10 21 14 3 17 12 21 12" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2" style="font-size:1.35rem;">Your Activity</h5>
                        <p class="text-muted mb-0" style="font-size:1.1rem;">View and manage your blog posts.</p>
                    </div>
                </a>
            </div>

            <!-- Profile Settings Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('profile.settings') }}" class="profile-card-link">
                    <div class="profile-card h-100 d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="mb-4">
                            <!-- Bootstrap Gear Fill Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#6366f1" class="bi bi-gear-fill" viewBox="0 0 16 16">
                              <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                              <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.693-1.115l.094-.319z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2" style="font-size:1.35rem;">Profile Settings</h5>
                        <p class="text-muted mb-0" style="font-size:1.1rem;">Update your profile information, email, password, etc.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <style>
        .profile-card-link {
            text-decoration: none;
            color: inherit;
        }
        .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px 0 rgba(60,72,88,0.18); /* Darker shadow */
            padding: 2.5rem 1.5rem 2rem 1.5rem;
            transition: box-shadow 0.2s, transform 0.2s;
            min-height: 320px;
        }
        .profile-card:hover, .profile-card:focus {
            box-shadow: 0 12px 48px 0 rgba(99,102,241,0.25); /* Darker hover shadow */
            transform: translateY(-4px) scale(1.025);
            border-color: #6366f1;
        }
        .profile-card svg {
            display: block;
            margin: 0 auto;
        }
        @media (max-width: 767px) {
            .profile-card {
                min-height: 220px;
                padding: 2rem 1rem 1.5rem 1rem;
            }
        }
    </style>
@endsection