@if (app()->isProduction())
<script async defer data-domain="{{ $tracking_id }}" src="https://plausible.ferranfigueredo.com/js/index.js"></script>
@endif