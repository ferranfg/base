<li class="mt-4">
    <div class="d-flex justify-content-between">
        <div class="media align-items-center">
            <span class="pr-3">
                <img src="{{ $comment->author_photo_url }}" class="img-fluid avatar avatar-md-sm rounded-circle shadow" alt="{{ $comment->author_name }}">
            </span>
            <div class="commentor-detail">
                <h6 class="mb-0"><span class="text-dark media-heading">{{ $comment->author_name }}</span></h6>
                <time class="text-muted" datetime="{{ $comment->created_at }}">{{ $comment->created_at_diff }}</time>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <p class="text-muted p-3 bg-light rounded">{{ $comment->content }}</p>
    </div>
</li>