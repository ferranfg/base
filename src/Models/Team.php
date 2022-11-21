<?php

namespace Ferranfg\Base\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Team
{
    use LogsActivity;

    /**
     * Configure the model activity logger.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
