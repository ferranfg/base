<link rel="canonical" href="{{ $product->canonical_url }}">
<link rel="alternate" href="{{ $product->canonical_url }}" hreflang="{{ config('app.locale') }}" />
<link rel="alternate" href="{{ $product->canonical_url }}" hreflang="x-default" />

<meta name="description" content="{{ $product->excerpt }}">

<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $product->name }}" />
<meta property="og:description" content="{{ $product->excerpt }}" />
<meta property="og:url" content="{{ $product->canonical_url }}" />
<meta property="og:image" content="{{ $photo_url }}" />
<meta property="article:published_time" content="{{ $product->created_at->toISOString() }}" />
<meta property="article:modified_time" content="{{ $product->updated_at->toISOString() }}" />

<meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}" />

<meta name="theme-color" content="#2f55d4" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $product->name }}" />
<meta name="twitter:description" content="{{ $product->excerpt }}" />
<meta name="twitter:url" content="{{ $product->canonical_url }}" />
<meta name="twitter:image" content="{{ $photo_url }}" />
<meta name="twitter:site" content="{{ config('base.twitter_username') }}" />
<meta property="og:image:width" content="1920" />
<meta property="og:image:height" content="1080" />

<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ $product->excerpt }}",
    "image": "{{ $photo_url }}",
    @if ($brand = $product->getMetadata('brand'))
        "brand": {
            "@type": "Brand",
            "name": "{{ $brand }}"
        },
    @endif
    "offers": {
        "@type": "Offer",
        "price": "{{ $product->amount }}",
        "priceCurrency": "{{ $product->currency }}",
        "availability": "http://schema.org/InStock",
        "priceValidUntil": "{{ now()->endOfYear()->toISOString() }}",
        "seller": {
            "@type": "Organization",
            "name": "{{ config('base.shop_title') }}",
            "url": "{{ url('shop') }}"
        },
        "hasMerchantReturnPolicy": {
            "@type": "MerchantReturnPolicy",
            "url": "{{ url('shop') }}"
        },
        "shippingDetails": {
            "@type": "OfferShippingDetails",
            "shippingRate": {
                "@type": "MonetaryAmount",
                "currency": "{{ config('base.shop_currency') }}",
                "value": "0"
            }
        }
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ $product->avgRating() ?: 5 }}",
        "reviewCount": "{{ $product->comments_disabled ? 1 : ($product->comments->count() ?: 1) }}"
    }
}
</script>