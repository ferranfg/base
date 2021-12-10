<form action="{{ $action }}" method="POST">
    @csrf
    @if (session('success'))
        <div class="alert alert-success">{{session('success')}}</div>
    @endif

    @if (isset($rating) and $rating == true)
        <div class="my-3 rating">
            <div class="stars">
                <label class="rate">
                    <input type="radio" name="rating" value="1">
                    <div class="face"></div>
                    <i class="far fa-star star one-star"></i>
                </label>
                <label class="rate">
                    <input type="radio" name="rating" value="2">
                    <div class="face"></div>
                    <i class="far fa-star star two-star"></i>
                </label>
                <label class="rate">
                    <input type="radio" name="rating" value="3">
                    <div class="face"></div>
                    <i class="far fa-star star three-star"></i>
                </label>
                <label class="rate">
                    <input type="radio" name="rating" value="4">
                    <div class="face"></div>
                    <i class="far fa-star star four-star"></i>
                </label>
                <label class="rate">
                    <input type="radio" name="rating" value="5">
                    <div class="face"></div>
                    <i class="far fa-star star five-star"></i>
                </label>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Comment</label>
                <div class="position-relative">
                    <i class="fa fa-comment icons"></i>
                    <textarea class="form-control pl-5 {{ $errors->has('content') ? 'is-invalid' : '' }}" name="content" placeholder="Comment" rows="4">{{ old('content') }}</textarea>
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
                <button type="submit" class="btn btn-primary btn-block">Comment</button>
            </div>
        </div>
    </div>
</form>