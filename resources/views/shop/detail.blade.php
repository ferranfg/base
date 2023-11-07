<div class="row">
    <div class="col-lg-6">
        <div id="carousel-{{ $product->slug }}" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ img_url($product->photo_url) }}" class="d-block w-100" />
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
            <h5 class="text-muted mb-4">{{ $product->formatAmount() }}</h5>
            <h5>Contenido:</h5>
            <div class="text-muted">
                @basedown($product->description)
            </div>
            @if ($product->status == 'out_of_stock')
                <div class="alert alert-warning d-inline-block mb-4">
                    <span class="fa fa-exclamation-triangle mr-2"></span> Producto No Disponible
                </div>
            @elseif ($product->type == 'affiliate')
                <div class="mb-4">
                    <a href="{{ $product->attached_url }}" class="btn btn-primary" rel="noreferrer nofollow">Ver Producto <span class="fa fa-external-link ml-1"></span></a>
                </div>
            @else
                <form class="mb-4" method="POST" action="{{ $product->canonical_url }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex shop-list align-items-center">
                                <h6 class="mb-0">Cantidad:</h6>
                                <div class="ml-3">
                                    <input type="button" value="-" class="minus btn btn-icon btn-soft-primary font-weight-bold">
                                    <input type="text" value="1" name="quantity" title="Cantidad" class="btn btn-icon btn-soft-primary font-weight-bold">
                                    <input type="button" value="+" class="plus btn btn-icon btn-soft-primary font-weight-bold">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" title="Añadir a la cesta">Añadir a la cesta <span class="fa fa-shopping-cart"></span></button>
                </form>
            @endif
            <h5>Compartir:</h5>
            <div>
                <a href="{{ $product->intentTweetUrl() }}" class="btn btn-info btn-sm" title="Compartir en Twitter" rel="noreferrer nofollow"><span class="fa fa-twitter"></span></a>
                <a href="{{ $product->intentFacebookUrl() }}" class="btn btn-primary btn-sm" title="Compartir en Facebook" rel="noreferrer nofollow"><span class="fa fa-facebook-square"></span></a>
                <a href="{{ $product->intentPinterestUrl() }}" class="btn btn-danger btn-sm" title="Compartir en Pinterest" rel="noreferrer nofollow"><span class="fa fa-pinterest"></span></a>
            </div>
        </div>
    </div>
</div>