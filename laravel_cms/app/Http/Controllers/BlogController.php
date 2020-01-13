<?php

namespace App\Http\Controllers;

use App\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Gallery;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $galleries = Gallery::all();
        $posts = Blog::all();
        return view('blogs.index')->withBlogs($posts)->withGalleries($galleries);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'contents' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $blog = new Blog();
        $blog['title']= $request->title;
        $blog->contents = $request->contents;

        $blog->user_id = $request->user()->id;
        $blog->page_path = $request->getPathInfo();
        $blog->save();

        $image = $request->file('image');
        if($image) {
            $extension = $image->getClientOriginalExtension();
            $filename = time() . '.' . $image->getFilename() . '.' . $extension;
            Storage::disk('public')->put($filename, File::get($image));

            $gallery = new Gallery;
            $gallery->description = $request->description;
            $gallery->mime = $image->getClientMimeType();
            $gallery->original_filename = $image->getClientOriginalName();
            $gallery->filename = $filename;
            $gallery->post_id=$blog->id;
            $gallery->save();
        }
        return redirect()->route('blogs.show', $blog);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {

        $comments = $blog->find($blog->id)->comments;
        return view('blogs.show')->withBlog($blog)->withComments($comments);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        return view('blogs.edit')->withBook($blog);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        $this->validate($request, [
            'title' => 'required',
            'contents' => 'required'
        ]);


        $blog['title']= $request->title;
        $blog->contents = $request->contents;
//        $blog->user = $request->user();
//        $blog->page_path = $request->getPathInfo();
        $blog->save();


        return redirect()->route('blogs.show', $blog);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        $images = Gallery::where('blog_id',$blog->id);
        foreach($images as $image) File::delete("uploads/" . $image->filename);
        $blog->delete();
        return redirect()->route('blogs.index');
    }
}