<link rel="canonical" href="{{ $post->canonical_url }}">
<link rel="alternate" href="{{ $post->canonical_url }}" hreflang="{{ config('app.locale') }}" />
<link rel="alternate" href="{{ $post->canonical_url }}" hreflang="x-default" />

<meta name="description" content="{{ $post->excerpt }}">

<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $post->name }}" />
<meta property="og:description" content="{{ $post->excerpt }}" />
<meta property="og:url" content="{{ $post->canonical_url }}" />
<meta property="og:image" content="{{ $photo_url }}" />
<meta property="article:published_time" content="{{ $post->created_at->toISOString() }}" />
<meta property="article:modified_time" content="{{ $post->updated_at->toISOString() }}" />

<meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}" />

<meta name="theme-color" content="#2f55d4" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $post->name }}" />
<meta name="twitter:description" content="{{ $post->excerpt }}" />
<meta name="twitter:url" content="{{ $post->canonical_url }}" />
<meta name="twitter:image" content="{{ $photo_url }}" />
@if ($post->author)
    <meta name="twitter:label1" content="By" />
    <meta name="twitter:data1" content="{{ $post->author->name }}" />
@endif
<meta name="twitter:site" content="{{ config('base.twitter_username') }}" />
<meta property="og:image:width" content="1920" />
<meta property="og:image:height" content="1080" />

<meta property="article:published_time" content="{{ $post->created_at->toISOString() }}" />
<meta property="article:modified_time" content="{{ $post->updated_at->toISOString() }}" />

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "publisher": {
        "@type": "Organization",
        "name": "{{ config('app.name') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ config('base.meta_image') }}",
            "width": 1920,
            "height": 1080
        }
    },
    @if ($post->author)
        "author": {
            "@type": "Person",
            "name": "{{ $post->author->name }}",
            "image": {
                "@type": "ImageObject",
                "url": "{{ $post->author->photo_url }}",
                "width": 200,
                "height": 200
            }
        },
    @endif
    "headline": "{{ $post->name }}",
    "url": "{{ $post->canonical_url }}",
    "datePublished": "{{ $post->created_at->toISOString() }}",
    "dateModified": "{{ $post->updated_at->toISOString() }}",
    "image": {
        "@type": "ImageObject",
        "url": "{{ $photo_url }}",
        "width": 1920,
        "height": 1080
    },
    "description": "{{ $post->excerpt }}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ config('app.url') }}"
    }
}
</script>