@php
    use Ferranfg\Base\Base;

    $display_showcase = request()->routeIs('blog.*');

    if ($display_showcase)
    {
        try
        {
            $showcase = Base::post()
                ->whereNotNull('showcase_product_ids')
                ->latest()
                ->first();
        }
        catch (Exception $e)
        {
            $display_showcase = false;
        }
    }
@endphp
@if ($display_showcase)
    <style>
        .swal2-close {
            position: absolute;
            top: 0;
        }
        .swal2-image {
            margin-bottom: 0;
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
        var displayedShowcase = false;

        function displayShowcase() {
            if (displayedShowcase) {
                window.removeEventListener('scroll', displayShowcase);

                return;
            }

            if (window.scrollY > 1400) {
                displayedShowcase = true;
                Swal.fire({
                    template: "#showcase-template",
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    showCloseButton: true,
                });
            }
        }

        window.addEventListener('scroll', displayShowcase);
    </script>
@endif