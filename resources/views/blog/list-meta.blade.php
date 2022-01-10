<meta name="description" content="{{ $meta_description }}">

<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $meta_title }}" />
<meta property="og:description" content="{{ $meta_description }}" />
<meta property="og:url" content="{{ $meta_url }}" />
<meta property="og:image" content="{{ $meta_image }}" />

<meta property="fb:app_id" content="{{ config('services.facebook.client_id') }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $meta_title }}" />
<meta name="twitter:description" content="{{ $meta_description }}" />
<meta name="twitter:url" content="{{ $meta_url }}" />
<meta name="twitter:image" content="{{ $meta_image }}" />
<meta name="twitter:site" content="{{ config('base.twitter_username') }}" />
<meta property="og:image:width" content="1920" />
<meta property="og:image:height" content="1280" />

{{ $posts->links('base::blog.pagination-meta') }}