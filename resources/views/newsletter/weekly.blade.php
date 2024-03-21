@foreach ($posts as $post)
## [{{ $post->name }}]({{ $post->canonical_url }})

![{{ $post->name }}]({{ $post->horizontal_photo_url }})

{{ mb_strlen($post->introduction) > mb_strlen($post->excerpt) ? $post->introduction : $post->excerpt }}

[{{ __('Read more') }}]({{ $post->canonical_url }})

@if ( ! $loop->last)
---

@endif
@endforeach