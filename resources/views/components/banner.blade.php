<section class="section">
    <div class="container-fluid">
        <div class="rounded shadow py-5 lazy" data-bg="url({{ hero_image() }})" style="background-position:center">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="rounded p-4 bg-light">
                            @include(config('base.banner_path'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>