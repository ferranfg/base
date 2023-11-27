@php
    $showcase = \Ferranfg\Base\Base::post()
        ->whereNotNull('showcase_product_ids')
        ->latest()
        ->first();
@endphp
@if ($showcase)
    <style>
        div:where(.swal2-container) img:where(.swal2-image) {
            margin: 0;
        }
    </style>
    <template id="showcase-template">
        <swal-image
            src="{{ img_url($showcase->photo_url, [['width' => 690, 'height' => 462]]) }}"
            alt="{{ $showcase->name }}" />
        <swal-html>
            <div style="margin:5px 0">
                <a href="{{ $showcase->canonical_url }}" class="text-indigo-400"><b>{{ $showcase->name }}</b></a>
            </div>
            <div>{{ $showcase->excerpt }}</div>
        </swal-html>
    </template>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            Swal.fire({
                template: "#showcase-template",
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
            });
        });
    </script>
@endif