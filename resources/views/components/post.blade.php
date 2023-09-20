<div class="card blog rounded border-0 shadow overflow-hidden mb-4">
    <div class="row align-items-center no-gutters">
        <div class="col-lg-4">
            {!! img($post->photo_url, 690, 462, true, 'img-fluid lazy', $post->name, 690, 462) !!}
            <div class="overlay bg-dark"></div>
            <div class="author">
                <small class="text-light user d-block"><i class="fa fa-user"></i> {{ $post->author->name }}</small>
                <small class="text-light date"><i class="fa fa-calendar"></i> Updated {{ $post->updated_at_diff }}</small>
                <time class="updated" datetime="{{ $post->updated_at }}"></time>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card-body content">
                <h2 class="h5">
                    <a href="{{ $post->canonical_url }}" class="card-title title text-dark" title="{{ $post->name }}">{{ $post->name }}</a>
                </h2>
                <p class="text-muted mb-0">{{ (strlen($post->excerpt) > 120) ? substr($post->excerpt, 0, 120) . '…' : $post->excerpt }}</p>
                <div class="post-meta d-flex justify-content-between mt-3">
                    @if ( ! $post->comments_disabled)
                        <ul class="list-unstyled mb-0">
                            <li class="list-inline-item">
                                <a href="{{ $post->canonical_url }}#comments" class="text-muted comments"><i class="fa fa-comments mr-1"></i> {{ $post->comments()->count() }}</a>
                            </li>
                        </ul>
                    @endif
                    <a href="{{ $post->canonical_url }}" class="text-muted readmore">Read more <i class="fa fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>