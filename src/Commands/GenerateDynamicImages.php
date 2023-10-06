<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Ferranfg\Base\Clients\Replicate;
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
    protected $signature = 'base:generate-dynamic-images {post} {--level=2}';

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
            $prompt = $post->keywords ?? $replace;

            $generate = Replicate::generate("{$prompt}, RAW candid cinema, 16mm, color graded portra 400 film, remarkable color, ultra realistic, textured skin, remarkable detailed pupils, realistic dull skin noise, visible skin detail, skin fuzz, dry skin, shot with cinematic camera", [
                'width' => 1264,
                'height' => 712
            ]);

            $image_name = Str::random(32) . '.webp';
            $image_url = Storage::url($image_name);

            Storage::put(
                $image_name,
                ImageManagerStatic::make($generate->upscaled_photo_url)->encode('webp', 90)
            );

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
