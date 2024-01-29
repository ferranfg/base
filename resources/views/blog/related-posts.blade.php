@if (isset($post) and $post->type == 'guide')
    <h5 class="mt-0">Related Guides:</h5>
    <ul class="list-unstyled mt-4 mb-0">
        @foreach ($related as $question)
            <li class="mt-2">
                <a href="{{ $question->canonical_url }}" class="text-muted"><span class="fa fa-arrow-right text-primary"></span> {{ $question->name }}</a>
            </li>
        @endforeach
    </ul>
@else
    <h5 class="mt-0">Related Posts:</h5>
    @foreach ($related as $related_post)
        @include('base::components.post', ['post' => $related_post, 'compact' => true])
    @endforeach
@endif