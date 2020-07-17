<?php

namespace Ferranfg\Base\Traits;

use Ferranfg\Base\Models\Metadata;

trait HasMetadata
{
    /**
     * Get the metadata of the model.
     */
    public function metadata()
    {
        return $this->morphMany(Metadata::class, 'parent');
    }

    /**
     * Quick update model metadata.
     */
    public function setMetadata($name, $value)
    {
        return $this->metadata()->save(
            new Metadata(['name' => $name, 'value' => $value])
        );
    }

    /**
     * Retrieve model metadata value.
     */
    public function getMetadata($name)
    {
        $result = $this->metadata()->select('value')->whereName($name)->first();

        return $result instanceof Metadata ? $result->value : null;
    }

}