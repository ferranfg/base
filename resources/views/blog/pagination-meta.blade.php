@if ($paginator->hasPages())
    @if ( ! $paginator->onFirstPage())
        <link rel="prev" href="{{ $paginator->previousPageUrl() }}">
    @endif

    @if ($paginator->hasMorePages())
        <link rel="next" href="{{ $paginator->nextPageUrl() }}">
    @endif
@endif