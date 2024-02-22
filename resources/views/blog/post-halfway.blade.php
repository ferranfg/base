@if ($h_index == 1 and $related->count())
    <div class="alert alert-light border-light pb-0 pt-4 px-4">
        @include('base::blog.related-posts', ['related' => $related->take(1)])
    </div>
@endif