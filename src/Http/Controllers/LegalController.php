<?php

namespace Ferranfg\Base\Http\Controllers;

use Parsedown;
use Illuminate\Routing\Controller;

class LegalController extends Controller
{
    /**
     * Show the selected page for the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($title, $filename)
    {
        $route = __DIR__ . '/../../../resources/views/legal';
        $locale = app()->getLocale();

        $file = file_exists("{$route}/{$filename}.{$locale}.md")
            ? "{$route}/{$filename}.{$locale}.md"
            : "{$route}/{$filename}.md";

        $content = (new Parsedown)->text(file_get_contents($file));

        $content = str_replace(':url', config('app.url'), $content);
        $content = str_replace(':name', config('app.name'), $content);

        return view('base::legal.placeholder', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * Show the terms of service for the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return $this->show(__('Terms Of Service'), 'terms');
    }

    /**
     * Muestra la página legal de política de cookies
     *
     * @return Response
     */
    public function cookies()
    {
        return $this->show(__('Cookie Policy'), 'cookies');
    }

    /**
     * Muestra la página legal de política de privacidad
     *
     * @return Response
     */
    public function privacy()
    {
        return $this->show(__('Privacy Policy'), 'privacy');
    }
}