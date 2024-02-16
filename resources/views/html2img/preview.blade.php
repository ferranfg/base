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
        body {
            font-family: "Nunito Sans", sans-serif;
            line-height: 0.5 !important;
        }
    </style>
</head>
<body class="flex flex-col bg-black">
    <div id="capture" class="mx-auto text-base" style="width:{{ $width }}px;height:{{ $height }}px;">
        <div class="relative h-full p-8 bg-cover bg-center bg-repeat" style="background-image:url({{ $background }})">
            @includeFirst(["html2img.{$template}", "base::html2img.{$template}"])
        </div>
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