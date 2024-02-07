<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"></script>

    <style>
        html {
            box-sizing: border-box;
        }

        *, *:before, *:after {
            box-sizing: inherit;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Nunito Sans", sans-serif;
        }
    </style>
</head>
<body class="flex flex-col bg-black text-center">
    <div id="capture"
        class="relative bg-white px-32 flex flex-col place-content-center mx-auto bg-cover bg-center bg-repeat"
        style="
            width:{{ $width }}px;
            height:{{ $height }}px;
            @if ($background)
                background-image:url({{ $background }})
            @endif
        ">
        @if ($background)
            <div class="absolute top-0 left-0 w-full h-full bg-black opacity-50"></div>
        @endif
        @if ($pre_title)
            <div id="pre-title" class="relative text-{{ $text_color }} text-xl mb-2">{{ $pre_title }}</div>
        @endif
        @if ($title)
            <div id="title" class="relative text-{{ $text_color }} text-5xl font-black mb-4">{{ $title }}</div>
        @endif
        @if ($description)
            <div id="description" class="relative text-{{ $text_color }} text-2xl font-light">{{ $description }}</div>
        @endif
    </div>
    <script>
        html2canvas(document.getElementById("capture"), {
            backgroundColor: null,
            useCORS: true,
            scale: 2,
            width: '{{ $width }}',
            height: '{{ $height }}',
        }).then(canvas => {
            axios.post('/html2img', {
                _token: '{{ csrf_token() }}',
                filename: '{{ $filename }}',
                image: canvas.toDataURL()
            }).then(response => {
                console.log(response.data)
            });
        });
    </script>
</body>
</html>