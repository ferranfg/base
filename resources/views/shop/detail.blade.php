<div class="row">
    <div class="col-lg-6">
        <div id="carousel-{{ $product->slug }}" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ $product->photo_url }}" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0 pt-2 pt-lg-0">
        <div class="sidebar sticky-bar">
            <div class="h5">
                {!! $product->renderAvgRating('review') !!}
            </div>
            <h2 class="title">{{ $product->name }}</h2>
            <h5 class="mt-4">Contenido:</h5>
            <div class="text-muted">
                @basedown($product->description)
            </div>
            <h5 class="mt-4">Compartir:</h5>
            <form method="POST" action="{{ $product->canonical_url }}">
                @csrf
                <div class="mb-4">
                    <a href="{{ $product->intentTweetUrl() }}" class="btn btn-info" title="Compartir en Twitter" rel="noreferrer nofollow"><span class="fa fa-twitter"></span></a>
                    <a href="{{ $product->intentFacebookUrl() }}" class="btn btn-primary" title="Compartir en Facebook" rel="noreferrer nofollow"><span class="fa fa-facebook-square"></span></a>
                    <a href="{{ $product->intentPinterestUrl() }}" class="btn btn-danger" title="Compartir en Pinterest" rel="noreferrer nofollow"><span class="fa fa-pinterest"></span></a>
                </div>
                <button type="submit" class="btn btn-primary" title="Añadir a la cesta">Añadir a la cesta <span class="fa fa-shopping-cart"></span></button>
            </form>
        </div>
    </div>
</div>