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
            $product = Base::product()->updateOrCreate([
                'slug' => Arr::get($product, 'asin'),
            ], [
                'name' => Arr::get($product, 'name'),
                'description' => Arr::get($product, 'about'),
                'attached_url' => Arr::get($product, 'short_url'),
                'photo_url' => Arr::get($product, 'image_1'),
                'currency' => 'eur',
                'amount' => bcmul(Arr::get($product, 'price', 0), 100),
                'type' => 'affiliate',
                'status' => 'in_stock',
            ]);
        }
    }
}