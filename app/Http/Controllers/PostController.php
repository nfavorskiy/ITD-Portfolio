<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Post::class);

        return view('posts.index');
    }

    /**
     * API endpoint for paginated posts.
     */
    public function apiIndex(Request $request)
    {
        $this->authorize('viewAny', Post::class);

        $query = Post::with('user:id,name,is_admin')
            ->select('id', 'title', 'content', 'user_id', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc');

        if ($request->has('mine') && $request->mine == '1' && auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        $perPage = $request->get('per_page', 10);
        $posts = $query->paginate($perPage);

        $posts->getCollection()->transform(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user_id' => $post->user_id,
                'author_name' => $post->user ? $post->user->name : 'Deleted User',
                'author_is_admin' => $post->user ? $post->user->is_admin : false,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        });

        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Post::class);

        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = new Post($validated);
        $post->user_id = auth()->id();
        $post->save();

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        $post->load('user:id,name,is_admin');

        $post->author_name = $post->user ? $post->user->name : 'Deleted User';
        $post->author_is_admin = $post->user ? $post->user->is_admin : false;

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}