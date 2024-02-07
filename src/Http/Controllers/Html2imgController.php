<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Html2imgController extends Controller
{
    use ValidatesRequests;

    /**
     * Show the image preview page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function preview(Request $request)
    {
        abort_unless($request->filename, 422, 'Filename is required.');

        return view('base::html2img.default', [
            'filename' => $request->filename,
            'background' => $request->background,
            'pre_title' => $request->pre_title,
            'title' => $request->title,
            'description' => $request->description,
            'text_color' => $request->get('text_color', 'slate'),
            'width' => $request->get('width', 960),
            'height' => $request->get('height', 540),
        ]);
    }

    /**
     * Save or generate the image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $this->validate($request, [
            'filename' => 'required',
            'image' => 'required',
        ]);

        Storage::put($request->filename, Image::make($request->image)->encode('png'));

        return response()->json([
            'url' => Storage::url($request->filename)
        ]);
    }
}