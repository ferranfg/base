@if ($products->count())
    <div class="row">
        @foreach ($products as $product)
            <div class="col-lg-3 col-md-6 col-12 mt-4 pt-2">
                <div class="card shop-list border-0 position-relative">
                    @if ($product->getMetadata('flag'))
                        <div class="ribbon ribbon-left ribbon-danger overflow-hidden">
                            <span class="text-center d-block shadow small h6 font-weight-bold">{{ $product->getMetadata('flag') }}</span>
                        </div>
                    @endif
                    <form method="POST" action="{{ $product->canonical_url}}" class="shop-image position-relative overflow-hidden rounded shadow p-3">
                        @csrf
                        <a href="{{ $product->canonical_url}}" data-toggle="modal" data-target="#ajax-modal" title="{{ $product->name }}">
                            {!! img($product->photo_url, 350, 350, true, 'img-fluid lazy', $product->name, 350, 350) !!}
                        </a>
                        <ul class="list-unstyled shop-icons">
                            <li class="mt-2">
                                <a href="{{ $product->canonical_url}}" class="btn btn-icon btn-pills btn-soft-primary" title="{{ $product->name }}"><i class="fa fa-eye"></i></a>
                            </li>
                            <li class="mt-2">
                                <button type="submit" class="btn btn-icon btn-pills btn-soft-warning"><i class="fa fa-shopping-cart" title="Añadir a Cesta de compra"></i></button>
                            </li>
                        </ul>
                    </form>
                    <div class="card-body content pt-4 p-2">
                        <a href="{{ $product->canonical_url}}" class="text-dark product-name h6">{{ $product->name }}</a>
                        <div class="d-flex justify-content-between mt-1">
                            <h6 class="text-muted small font-italic mb-0 mt-1">
                                @if ($product->isDiscounted())
                                    {{ $product->formatSaleAmount() }} <del class="text-danger ml-2">{{ $product->formatAmount() }}</del>
                                @else
                                    {{ $product->formatAmount() }}
                                @endif
                            </h6>
                            {!! $product->renderAvgRating('review') !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif