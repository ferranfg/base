<?php

namespace Ferranfg\Base\Http\Controllers;

use Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function resize(Request $request, $url)
    {
        $base = base64_encode((string) Storage::get($url));
        $jpeg = imagecreatefromjpeg("data://application/octet-stream;base64,{$base}");

        imagewebp($jpeg, $url);

        $image = new ImageResize($url);

        if ($request->width and is_null($request->heigth)) $image->resizeToWidth($request->width);

        return $image->output();
    }

}