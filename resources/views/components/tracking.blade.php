@if (app()->isProduction() and config('base.tracking_id'))
<script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "{{ config('base.tracking_id') }}"}'></script>
@endif