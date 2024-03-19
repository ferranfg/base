@foreach ($posts as $post)
## [{{ $post->name }}]({{ $post->canonical_url }})

![{{ $post->name }}]({{ $post->horizontal_photo_url }})

{{$post->excerpt }}

[{{ __('Read more') }}]({{ $post->canonical_url }})

@if ( ! $loop->last)
---

@endif
@endforeach