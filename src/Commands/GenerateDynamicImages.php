<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Ferranfg\Base\Clients\Replicate;
use Ferranfg\Base\Clients\Unsplash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;

class GenerateDynamicImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:generate-dynamic-images {post} {--level=2} {--method=unsplash}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take the post keywords and generate dynamic images.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $post = Base::post()->find($this->argument('post'));

        if (is_null($post)) return Command::FAILURE;

        // Buscamos un punto final, salto de linea y el tercer h2
        preg_match_all('/\.\n\n## (.+)/i', $post->content, $matches);

        if (array_key_exists(1, $matches) and array_key_exists($this->option('level'), $matches[1]))
        {
            $replace = $matches[1][$this->option('level')];

            $image_name = Str::random(32) . '.webp';
            $image_url = Storage::url($image_name);
            $image_content = null;

            if ($this->option('method') == 'replicate')
            {
                $prompt = $post->keywords ?? $replace;

                $generate = Replicate::generate("{$prompt}, RAW candid cinema, 16mm, color graded portra 400 film, remarkable color, ultra realistic, textured skin, remarkable detailed pupils, realistic dull skin noise, visible skin detail, skin fuzz, dry skin, shot with cinematic camera", [
                    'width' => 1264,
                    'height' => 712
                ]);

                $image_content = ImageManagerStatic::make($generate->upscaled_photo_url)->encode('webp', 90);
            }
            else if ($this->option('method') == 'unsplash')
            {
                if ( ! $post->keywords or ! config('services.unsplash.collections')) return Command::FAILURE;

                $images = Unsplash::search($post->keywords, 1, 30, 'landscape');

                if ($images->count())
                {
                    $unsplash_url = $images->pluck('urls.regular')->random();

                    $image_content = ImageManagerStatic::make($unsplash_url)->encode('webp', 90);
                }
            }

            if (is_null($image_content)) return Command::FAILURE;

            Storage::put($image_name, $image_content);

            $post->content = str_replace(
                ".\r\n\r\n## {$replace}",
                ".\r\n\r\n![{$post->name}]({$image_url})\r\n\r\n## {$replace}",
                $post->content
            );

            $post->save();
        }

        return Command::SUCCESS;
    }
}
