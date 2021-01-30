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
        $this->metadata()->updateOrCreate(['name' => $name], ['value' => $value]);

        return $value;
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