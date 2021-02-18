<?php

namespace Ferranfg\Base\Models;

use Laravel\Spark\Team as SparkTeam;
use Venturecraft\Revisionable\RevisionableTrait;

class Team extends SparkTeam
{
    use RevisionableTrait;
}
