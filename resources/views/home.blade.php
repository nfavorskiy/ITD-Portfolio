@extends('layouts.app')

@section('title', 'Home - ' . config('app.name'))

@section('content')
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">Home</h2>
    </x-slot>

    <div class="container text-center mt-5">
        <h1 class="display-4">Welcome to ITD Portfolio!</h1>
        <p class="lead">This is a simple Laravel + Bootstrap CRUD app for Posts.</p>
        @guest
            <a href="{{ route('login', ['redirect_to_posts' => 'true']) }}" class="btn btn-primary">Sign in to view Posts</a>
        @else
            <a href="{{ route('posts.index') }}" class="btn btn-primary">View Posts</a>
        @endguest
    </div>
@endsection