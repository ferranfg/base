<?php

namespace Ferranfg\Base\Traits;

trait HasVisits
{
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