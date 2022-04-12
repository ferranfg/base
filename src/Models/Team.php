<?php

namespace Ferranfg\Base\Models;

use Spatie\Activitylog\LogOptions;
use Laravel\Spark\Team as SparkTeam;
use Spatie\Activitylog\Traits\LogsActivity;

class Team extends SparkTeam
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
