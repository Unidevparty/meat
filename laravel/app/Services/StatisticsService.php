<?php


namespace App\Services;

use App\IPB;
use \IPS\Member;

class StatisticsService
{
    public static function getGroups()
    {
        $groups = IPB::groups();

        $groupName = [];
        foreach($groups as $group){
            $groupName[$group['id']] = $group['name'];
        }

        return $groupName;
    }

    public static function getPercent($count, $total)
    {
        return ($count / $total) * 100;
    }

}