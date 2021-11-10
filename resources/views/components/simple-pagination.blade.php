@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <a class="btn btn-light disabled">@lang('pagination.previous')</a>
        @else
            <a class="btn btn-light" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="btn btn-light" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
        @else
            <a class="btn btn-light disabled">@lang('pagination.next')</a>
        @endif
    </nav>
@endif