<div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded shadow border-0">
        <div class="modal-header">
            <h5 class="modal-title">{{ $product->name }}</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body p-4">
            <div class="container-fluid px-0" style="min-height:718px">
                @include('base::shop.detail', ['product' => $product])
            </div>
        </div>
    </div>
</div>