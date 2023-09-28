<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Illuminate\Console\Command;

class InternalLinking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:internal-linking {--debug=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create internal links for given post.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (Base::post()->whereStatus('published')->get() as $post)
        {
            if (is_null($post->keywords)) continue;

            // $post habla sobre las siguientes keywords
            $keywords = explode(', ', $post->keywords);

            foreach ($keywords as $keyword)
            {
                // Buscamos posts que tengan en el contenido la keyword
                // No hay espacio final en el like por si hay signos de puntuación
                Base::post()->where('content', 'LIKE', "% {$keyword}%")
                    ->where('id', '!=', $post->id)
                    ->get()
                    ->each(function ($edit) use ($keyword, $post)
                    {
                        // Reemplazamos la keyword por un enlace interno al post
                        $edit->content = str_replace($keyword, "[{$keyword}]({$post->internal_link})", $edit->content);
                        $edit->timestamps = false;

                        $this->info("Post: {$edit->name}");
                        $this->info("Keyword: {$keyword}");
                        $this->info("Link to: {$post->internal_link}");

                        if ( ! $this->option('debug')) $edit->save();
                    });
            }
        }

        return Command::SUCCESS;
    }
}