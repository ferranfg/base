<?php

namespace Ferranfg\Base\Repositories;

use Carbon\Carbon;
use Ferranfg\Base\Base;
use Ferranfg\Base\Models\Metadata;

class ProductRepository
{
    public function whereType($type)
    {
        return Base::product()->with('comments', 'metadata')->whereType($type);
    }

    public function whereIn($column, $values)
    {
        return Base::product()->whereIn($column, $values);
    }

    public function whereAvailable()
    {
        return Base::product()
            ->with('comments', 'metadata')
            ->whereIn('status', ['available', 'in_stock'])
            ->whereIn('type', ['simple', 'affiliate']);
    }

    public function randomAvailable()
    {
        return $this->whereAvailable()->inRandomOrder(Carbon::now()->format('Ymd'))->first();
    }

    public function findById($id)
    {
        return Base::product()->findOrFail($id);
    }

    public function findBySlug(string $slug, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Base::product()->where("slug->{$locale}", $slug)->firstOrFail();
    }

    public function previousProduct($product)
    {
        $key_name = Base::product()->getKeyName();
        $previous_id = Base::product()
            ->whereType($product->type)
            ->whereStatus($product->status)
            ->where($key_name, '<', $product->$key_name)
            ->max($key_name);

        return Base::product()->find($previous_id);
    }

    public function nextProduct($product)
    {
        $key_name = Base::product()->getKeyName();
        $next_id = Base::product()
            ->whereType($product->type)
            ->whereStatus($product->status)
            ->where($key_name, '>', $product->$key_name)
            ->min($key_name);

        return Base::product()->find($next_id);
    }

    public function getBrands()
    {
        return Metadata::whereName('brand')->groupBy('value');
    }

    public function getGoogleCategories()
    {
        return Metadata::whereName('google_category')->groupBy('value');
    }
}