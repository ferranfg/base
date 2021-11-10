@if ($previous or $next)
    <nav class="d-flex justify-content-between mt-5 mb-5">
        {{-- Previous Page Link --}}
        @if (is_null($previous))
            <a class="btn btn-light disabled">@lang('pagination.previous')</a>
        @else
            <a class="btn btn-light" href="{{ $previous->canonical_url }}" rel="prev">@lang('pagination.previous')</a>
        @endif

        {{-- Next Page Link --}}
        @if ( ! is_null($next))
            <a class="btn btn-light" href="{{ $next->canonical_url }}" rel="next">@lang('pagination.next')</a>
        @else
            <a class="btn btn-light disabled">@lang('pagination.next')</a>
        @endif
    </nav>
@endif