<div class="media customer-testi m-2">
    <img src="{{ $review->author_url }}" class="avatar avatar-small mr-3 rounded-circle shadow">
    <div class="media-body content p-3 shadow rounded bg-white position-relative">
        <ul class="list-unstyled mb-0">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $review->rating)
                    <li class="list-inline-item"><i class="fa fa-star text-warning"></i></li>
                @else
                    <li class="list-inline-item"><i class="fa fa-star text-muted"></i></li>
                @endif
            @endfor
        </ul>
        <p class="text-muted mt-2">{{ $review->content }}</p>
        <h6 class="text-primary">{{ $review->author_name }} <small class="text-muted">- Barcelona</small></h6>
    </div>
</div>