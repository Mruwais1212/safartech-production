<?php

namespace App\Services;

class DistanceService
{
    public function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'M')
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $distance = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $distance = acos($distance);
            $distance = rad2deg($distance);
            $distance = $distance * 60 * 1.1515;

            if ($unit == 'K') {
                $distance = $distance * 1.609344;
            } elseif ($unit == 'N') {
                $distance = $distance * 0.8684;
            } elseif ($unit == 'M') {
                $distance = $distance * 1.609344 * 1000;
            }

            return $distance;
        }
    }
}
