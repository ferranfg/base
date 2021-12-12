<?php

namespace Ferranfg\Base\Models;

use Laravel\Spark\Team as SparkTeam;
use Spatie\Activitylog\Traits\LogsActivity;

class Team extends SparkTeam
{
    use LogsActivity;
}
