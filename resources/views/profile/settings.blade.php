@php
    $links = [
        'Your Profile' => route('profile.index'),
        'Profile Settings' => '',
    ];
@endphp

@extends('layouts.app')

@section('title', Auth::user()->name . ' - Profile Settings - ' . config('app.name'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="mb-4">Profile Settings</h2>
                
                <!-- Profile Information Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Password Update Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection