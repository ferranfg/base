<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ImportProductsOutscraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:import-products-outscraper {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Import Amazon products from Outscraper URL';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = "outscraper-" . time() . ".json";

        Storage::put($filename, file_get_contents($this->argument('url')));

        $products = json_decode(Storage::get($filename), true);
        $products = collect($products)->unique('asin');

        foreach ($products as $product)
        {
            $name = Arr::get($product, 'name');
            $slug = Arr::get($product, 'details_isbn-13');
            $description = Arr::get($product, 'description');
            $image = Arr::get($product, 'image_1');

            if ( ! $slug) $slug = Arr::get($product, 'asin');
            if ( ! $description) $description = Arr::get($product, 'about');
            if (str_contains($image, 'pixel')) $image = Arr::get($product, 'image_2');

            $slug = str_replace('-', (string) null, $slug);

            $product = Base::product()->updateOrCreate([
                'slug' => $slug,
            ], [
                'owner_id' => 1,
                'name' => implode(' ', array_slice(explode(' ', $name), 0, 7)),
                'slug' => $slug,
                'description' => "{$name}. {$description}",
                'attached_url' => Arr::get($product, 'short_url'),
                'photo_url' => $image,
                'currency' => 'eur',
                'amount' => Arr::get($product, 'price', 0),
                'type' => 'affiliate',
                'status' => 'in_stock',
            ]);
        }
    }
}