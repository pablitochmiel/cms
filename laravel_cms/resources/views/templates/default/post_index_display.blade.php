<h2>Posts:</h2>
@if($user && ($page->user_id==$user->id || $coworkers->user_id==$user->id))
    <a href="{{ url($page->page_path.'/create') }}">Create new...</a>
@endif
<ul>

    @forelse($blogs as $blog)
        <li>
            @includeIf("templates." . "default"  . ".title_display")
            <br>
            @if( $blog->page->page_type == "blog"):
            <a href ="{{ route('blog.show', [ 'blog' => $blog, 'user' => $page->user->page_name, 'path' => $page->page_name ]) }}"><strong>show</strong></a>
            @elseif( $blog->page->page_type == "gallery"):
            <a href ="{{ route('gallery.show', [ 'gallery' => $blog, 'user' => $page->user->page_name, 'path' => $page->page_name ]) }}"><strong>show</strong></a>
            @endif

            <div>
                @if($galleries=\App\Gallery::where('blog_id', $blog->id)->first())
                    @includeIf("templates." . "default"  . ".image_display")
                @endif
                <p>@markdown($blog->contents)</p>
            </div>
        </li>
    @empty
        <p>No posts.</p>
    @endforelse
</ul>
{{ $blogs->render() }}
