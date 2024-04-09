<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @section('index')
        @if (Str::contains(config('app.url'), request()->getHost()))
            <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
            <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
            <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
        @else
            <meta name="robots" content="noindex, nofollow">
            <meta name="googlebot" content="noindex, nofollow">
            <meta name="bingbot" content="noindex, nofollow">
        @endif
    @show

    <title>@yield('title', isset($meta_title) ? $meta_title : config('app.name'))</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha512-MoRNloxbStBcD8z3M/2BmnT+rg4IsMxPkXaGh2zD6LGNNFE80W3onsAhRcMAMrSoyWL9xD7Ert0men7vR8LUZg==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.1/sweetalert2.min.css" integrity="sha512-l1vPIxNzx1pUOKdZEe4kEnWCBzFVVYX5QziGS7zRZE4Gi5ykXrfvUgnSBttDbs0kXe2L06m9+51eadS+Bg6a+A==" crossorigin="anonymous" />
    @stack('styles')
    <link rel="stylesheet" href="{{ mix('css/web.css') }}" >

    @if (config('base.headers_font_family'))
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family={{ urlencode(config('base.headers_font_family')) }}&display=swap" rel="stylesheet">
        <style>
            .h1, .h2, .h3, .h4, .h5, .h6,
            h1, h2, h3, h4, h5, h6,
            .logo, .logo-footer {
                font-family: "{{ config('base.headers_font_family') }}";
                font-weight: 400;
            }
        </style>
    @endif

    @stack('head')

    <script>
        window.Spark = @json(Base::scriptVariables())
    </script>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://ik.imagekit.io">
    <link rel="preconnect" href="https://cdnjs.f13o.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <div id="spark-app" v-cloak>
        <div id="preloader" style="display:none">
            <div id="status" style="display:none">
                <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>
        </div>

        @section('header')
            <header id="topnav" class="defaultscroll sticky">
                <div class="container">
                    <div>
                        @section('logo')
                            <a class="logo" title="{{ config('app.name') }}" href="/">
                                <span class="l-light text-white">{{ config('app.name') }}</span>
                                <span class="l-dark">{{ config('app.name') }}</span>
                            </a>
                        @show
                    </div>
                    <div class="buy-button">
                        @yield('button')
                    </div>
                    <div class="menu-extras">
                        <div class="menu-item">
                            <a class="navbar-toggle">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div id="navigation">
                        @yield('navigation')
                    </div>
                </div>
            </header>
        @show

        @yield('main')

        @if (session('info'))
            <div class="fixed-bottom">
                <div class="container">
                    <div class="alert alert-info alert-dismissible fade show">
                        {!! session('info') !!}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                </div>
            </div>
        @endif

        @section('footer')
            <footer class="footer footer-bar">
                <div class="container">
                    <p class="mb-0">© {{ date('Y') }} {{ config('app.name') }} by <a href="{{ config('base.copyrigth_url') }}" target="_blank" rel="noopener nofollow">{{ str_replace('https://', '', config('base.copyrigth_url')) }}</a>. {{__('All rights reserved.')}}</p>
                </div>
            </footer>

            @if ($username = config('base.feedback_username') and $url = config('app.url'))
                <a href="{{ "https://twitter.com/intent/tweet?text=@{$username} feedback for {$url}" }}" target="_blank" rel="noopener nofollow" class="btn btn-icon btn-soft-primary fixed-bottom mb-4 mr-4" title="Feedback" style="left:auto">
                    <i class="icons fas fa-bullhorn"></i>
                </a>
            @else
                <a href="/" class="btn btn-icon btn-soft-primary back-to-top">
                    <i class="icons fa fa-chevron-up"></i>
                </a>
            @endif
        @show

        @includeWhen(config('base.newsletter_modal'), 'base::components.newsletter-modal')
    </div>

    <div class="modal fade" id="ajax-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    @includeWhen(config('base.cookie_consent'), 'cookie-consent::index')

    <script src="https://cdnjs.f13o.com/fontawesome.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha512-kBFfSXuTKZcABVouRYGnUo35KKa1FBrYgwG4PAx7Z2Heroknm0ca2Fm2TosdrrI356EDHMW383S3ISrwKcVPUw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/12.3.0/lazyload.min.js" integrity="sha512-z+ZJBKAzuh8g8lp/QTwk1EKLoeQlJoDm9Ur+5/FEi2DX5FsnAtoAv33fqOWtn5OUeBnp1j8T0uOK5gGb7xzJyQ==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.1/sweetalert2.min.js" integrity="sha512-lhtxV2wFeGInLAF3yN3WN/2wobmk+HuoWjyr3xgft42IY0xv4YN7Ao8VnYOwEjJH1F7I+fadwFQkVcZ6ege6kA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('scripts')
    <script src="{{ mix('js/web.js') }}"></script>

</body>
</html>
