<?php

namespace Ferranfg\Base\Commands;

use Apaapi\operations\SearchItems;
use Apaapi\lib\Request;
use Apaapi\lib\Response;
use Ferranfg\Base\Base;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportProductsAmazon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:import-products-amazon {keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Import Amazon products from Amazon Affiliate API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = "amazon-" . time() . ".json";

        $operation = new SearchItems();
        $operation->setPartnerTag(config('services.amazon.tag'))->setKeywords($this->argument('keyword'));

        $request = new Request(config('services.amazon.key'), config('services.amazon.secret'));
        $request->setLocale('es')->setPayload($operation);

        $response = new Response($request);

        Storage::put($filename, $response->get());

        $products = json_decode(Storage::get($filename), true);

        foreach ($products->SearchResult->Items as $product)
        {
            $product = Base::product()->updateOrCreate([
                'slug' => $product->ASIN,
            ], [
                'owner_id' => 1,
                'name' => $product->ItemInfo->Title->DisplayValue,
                'description' => (string) null,
                'attached_url' => $product->DetailPageURL,
                'photo_url' => $product->Images->Primary->Large->URL,
                'currency' => 'eur',
                'amount' => bcmul((float) $product->Offers->Listings[0]->Price->Amount, 100),
                'type' => 'affiliate',
                'status' => 'in_stock',
            ]);
        }
    }
}