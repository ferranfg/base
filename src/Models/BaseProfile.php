<?php

namespace Ferranfg\Base\Models;

use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class BaseProfile
{
    public $userAgent;

    public $location;

    public $grade;

    public function __construct(Request $request)
    {
        $this->userAgent = $this->parseUserAgent($request->userAgent(), $request->headers->all());
        $this->location = $this->parseLocation($request->ip());

        $this->grade = $this->estimateGrade();
    }

    /**
     * Parse user location
     */
    public function parseUserAgent($userAgent, $headers = [])
    {
        $agent = new Agent;

        $agent->setUserAgent($userAgent);
        $agent->setHttpHeaders($headers);

        return (object) [
            'browser' => $agent->browser(),
            'browserVersion' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platformVersion' => $agent->version($agent->platform()),
            'device' => $agent->device(),
            'isDesktop' => $agent->isDesktop(),
            'isPhone' => $agent->isPhone(),
            'isTablet' => $agent->isTablet(),
        ];
    }

    /**
     * Parse user location
     */
    public function parseLocation($ip)
    {
        $location = [];

        if (file_exists(storage_path('GeoLite2-City.mmdb')))
        {
            $geo = new Reader(storage_path('GeoLite2-City.mmdb'));

            $location['geo'] = $geo->city($ip)->raw;
        }

        if (file_exists(storage_path('GeoLite2-ASN.mmdb')))
        {
            $asn = new Reader(storage_path('GeoLite2-ASN.mmdb'));

            $location['asn'] = $asn->asn($ip)->raw;
        }

        return json_decode(json_encode($location, JSON_FORCE_OBJECT));
    }

    /**
     * Estimate user grade from 0 to 10.
     */
    public function estimateGrade()
    {
        $grade = 5;

        if (in_array($this->userAgent->device, ['iPhone'])) $grade = 7;
        if (in_array($this->userAgent->device, ['Macintosh', 'iPad'])) $grade = 8;

        return $grade;
    }
}