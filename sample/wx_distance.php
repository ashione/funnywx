<?php
define('LONGITUDE',120.12607);
define('LATITUDE',30.26126);
define('EARTH_RADIUS',6378.137);
function rad($degree){
    return $degree*M_PI/180.0;
}
function distance($lat1, $lng1)
{
     $earthRadius = EARTH_RADIUS; //approximate radius of earth in meters

     /*
       Convert these degrees to radians
       to work with the formula
     */

     $lat1 = ($lat1 * pi() ) / 180;
     $lng1 = ($lng1 * pi() ) / 180;

     $lat2 = (LATITUDE * pi() ) / 180;
     $lng2 = (LONGITUDE * pi() ) / 180;

     /*
       Using the
       Haversine formula

       http://en.wikipedia.org/wiki/Haversine_formula

       calculate the distance
     */

     $calcLongitude = $lng2 - $lng1;
     $calcLatitude = $lat2 - $lat1;
     $stepOne = pow(sin($calcLatitude / 2.0), 2.0) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2.0), 2.0);
     $stepTwo = 2.0 * asin(min(1.0, sqrt($stepOne)));
     $calculatedDistance = $earthRadius * $stepTwo;

     return round($calculatedDistance*1000000)/1000000;
}
#
#function distance($latitude,$longitude){
#     $radLat1 = rad($latitude);
#     $radLat2 = rad(LATITUDE);
#     $a = $radLat1 - $radLat2;
#     $b = rad($longitude) - rad(LONGITUDE);
#     //$s = 2 * asin(sqrt(pow(sin($a/2),2) +
#     //     cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
#     //$s = $s * EARTH_RADIUS;
#     $c = sin($radLat1)*sin($radLat2)+cos($radLat1)*cos($radLat2)*cos($b);
#     $s = EARTH_RADIUS*acos($c);#*M_PI/180.0;
#     $s = round($s * 10000) / 10000;
#    return $s;
#}
?>
