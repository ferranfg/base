<?php

namespace Ferranfg\Base\Traits;

use Illuminate\Support\Facades\DB;

trait HasVisits
{
    /**
     * Añade el método orderByVisits().
     */
    public function scopeOrderByVisits($query)
    {
        // Eliminamos del JSON la parte de {"es":" = 7
        return $query->select(
                DB::raw('cast(substring(metadata.value, 7) as unsigned) as visits'),
                'products.*'
            )
            ->leftJoin('metadata', function ($join)
            {
                $join->on('products.id', 'metadata.parent_id');
                $join->where('metadata.name', '_visits');
                $join->where('metadata.parent_type', get_class($this));
            })
            ->orderBy('visits', 'desc');
    }

    /**
     * Devuelve el número de visitas de una página.
     *
     * return int
     */
    public function getVisitsAttribute()
    {
        return (int) $this->getMetadata('_visits');
    }

    /**
     * Increases the _visits metadata with 1 visit
     */
    public function trackVisit()
    {
        $visits = $this->getMetadata('_visits');

        if (is_null($visits)) $visits = 0;

        $visits++;

        $this->setMetadata('_visits', $visits);

        return $this;
    }
}