<?php

namespace Ferranfg\Base\Http\Controllers;

use Barryvdh\Debugbar\Facades\Debugbar;
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
        Debugbar::disable();

        abort_unless($request->filename and $request->template, 422, 'Filename and template is required.');

        return view('base::html2img.preview', [
            'filename' => $request->filename,
            'template' => $request->template,
            'background' => img_url($request->get('background', hero_image()), [
                ['width' => 1080, 'height' => 1080]
            ]),
            'pre_title' => $request->get('pre_title', config('app.name')),
            'title' => $request->title,
            'description' => $request->description,
            'width' => 540,
            'height' => 540,
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