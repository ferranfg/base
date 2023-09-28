<?php

namespace Ferranfg\Base\Clients;

use Illuminate\Support\Arr;
use BenBjurstrom\Replicate\Replicate as ReplicateClient;

class Replicate
{
    public static function generate($prompt)
    {
        $input = [
            'width' => 512,
            'height' => 512,
        ];

        $replicate = new ReplicateClient(config('services.replicate.secret'));

        $prediction = $replicate->predictions()->create(
            config('services.replicate.key'),
            array_merge($input, ['prompt' => $prompt])
        );

        $pending = true;

        while ($pending)
        {
            $prediction = $replicate->predictions()->get($prediction->id);

            $pending = $prediction->status !== 'succeeded';

            if ($pending) sleep(3);
        }

        return (object) [
            'imagine_message_id' => $prediction->id,
            'upscaled_photo_url' => Arr::get($prediction->output, 0)
        ];
    }
}