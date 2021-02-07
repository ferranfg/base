<form action="{{ $action }}" method="POST">
    @csrf
    @if (session('success'))
        <div class="alert alert-success">{{session('success')}}</div>
    @endif

    @if (isset($rating) and $rating == true)
        <div class="mt-4 mb-4">
            <h6 class="small font-weight-bold">Valoración</h6>
            <div class="d-inline-block">
                <ul class="list-unstyled mb-0 small">
                    <li class="list-inline-item"><i class="fa fa-star-o text-warning"></i></li>
                    <li class="list-inline-item"><i class="fa fa-star-o text-warning"></i></li>
                    <li class="list-inline-item"><i class="fa fa-star-o text-warning"></i></li>
                    <li class="list-inline-item"><i class="fa fa-star-o text-warning"></i></li>
                    <li class="list-inline-item"><i class="fa fa-star-o text-warning"></i></li>
                </ul>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Comentario</label>
                <div class="position-relative">
                    <i class="fa fa-comment icons"></i>
                    <textarea class="form-control pl-5 {{ $errors->has('content') ? 'is-invalid' : '' }}" name="content" placeholder="Comentario" rows="4">{{ old('content') }}</textarea>
                    @if ($errors->has('content'))
                        <div class="invalid-feedback">{{ $errors->first('content') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <i class="fa fa-user icons"></i>
                    <input class="form-control pl-5 {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Name') }}">
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label>{{ __('Email') }} <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <i class="fa fa-envelope icon-sm icons"></i>
                    <input class="form-control pl-5 {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}">
                    @if ($errors->has('email'))
                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="send">
                <button type="submit" class="btn btn-primary btn-block">Enviar</button>
            </div>
        </div>
    </div>
</form>