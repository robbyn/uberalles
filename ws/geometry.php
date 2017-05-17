<?php
// Earth geometry constants
define("R", 6371030.0); // average radius
define("A", 6378137.0); // smallest radius
define("B", 6356752.3142); // largest radius
define("A2", A*A);
define("B2", B*B);
define("ELEVATION", 400); // use a constant elevation of 400m

// travel time estimation
define("TRAVEL_MIN", 240);
define("TRAVEL_FACT", 60/250); // one minute for 250m

// returns the distance in meters between two points on the surface of the earth
function distance($dlat1, $dlng1, $dlat2, $dlng2, $ele) {
    $lat1 = deg2rad($dlat1);
    $lng1 = deg2rad($dlng1);
    $lat2 = deg2rad($dlat2);
    $lng2 = deg2rad($dlng2);
    $cosP = cos($lat1);
    $sinP = sin($lat1);
    $s2 = A2 * $cosP * $cosP + B2 * $sinP * $sinP;
    $s = sqrt($s2);
    $sq1 = ($lat1 - $lat2) * ($ele + A2*B2/($s*$s2));
    $sq2 = ($lng1 - $lng2) * ($ele + A2/$s)*$cosP;
    return sqrt($sq1 * $sq1 + $sq2 * $sq2);
}

// estimated time in seconds for the taxi to travel a distance given in meters
function travel_time($dist) {
    return TRAVEL_MIN + $dist*TRAVEL_FACT;
}
?>
