<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function apiIndex(Request $request)
    {
        if ($request->has('mine') && auth()->check()) {
            $posts = Post::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        } else {
            $posts = Post::orderBy('created_at', 'desc')->get();
        }
        return response()->json($posts);
    }
    
    public function index(Request $request)
    {
        if ($request->has('mine') && auth()->check()) {
            $posts = Post::where('user_id', auth()->id())->get();
        } else {
            $posts = Post::all();
        }
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('posts.index')->with('post_created', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        if (auth()->user()->id !== $post->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->user()->id !== $post->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required',
        ]);

        $post->update($request->all());

        return redirect()->route('posts.index')->with('post_updated', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        if (auth()->user()->id !== $post->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $post->delete();
        return redirect()->route('posts.index')->with('post_deleted', 'Post deleted.');
    }
}