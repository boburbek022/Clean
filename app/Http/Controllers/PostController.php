<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::paginate(3);

        return view('posts.index')->with('posts',$posts);

    }

    public function create()
    {
        return view('posts.create');
    }


    public function store(StorePostRequest $request)
    {
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('post-photos');
        }

        $posts = Post::create([
            'title' => $request->title,
            'Content' => $request->Content,
            'short_content' => $request->short_content,
            'photo' => $path ?? null,
        ]);
        return redirect()->route('posts.index');
    }


    public function show(Post $post)
    {
        return view('posts.show')->with([
            'post' => $post,
            'recent_posts' => Post::latest()->get()->except($post->id)->take(5)
        ]);
    }


    public function edit(Post $post)
    {
        return view('posts.edit')->with([
            'post' => $post
        ]);
    }


    public function update(StorePostRequest $request, Post $post)

    {
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('post-photos');

            if (isset($post->photo)) {
                Storage::delete($post->photo);
            }
        }

        $post->update([
            'title' => $request->title,
            'Content' => $request->Content,
            'short_content' => $request->short_content,
            'photo' => $path ??  $post->photo
        ]);

        return redirect()->route('posts.show', ['post' => $post->id]);
    }


    public function destroy(Post $post)
    {
        if(isset($post->photo)){
            Storage::delete($post->photo);
        }
        $post->delete();

        return redirect()->route('posts.index');
    }
}
