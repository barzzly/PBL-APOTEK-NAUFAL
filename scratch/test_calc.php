<?php
$lat1 = -0.937722;
$lng1 = 100.3878982;
$lat2 = -0.8655057;
$lng2 = 100.3441059;

function calc_haversine($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

$haversine = calc_haversine($lat1, $lng1, $lat2, $lng2);
echo "Haversine: " . $haversine . " km\n";

$osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$lng1},{$lat1};{$lng2},{$lat2}?overview=full&geometries=geojson";
$opts = [
    'http' => [
        'header' => "User-Agent: ApotekNaufalApp/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$res = file_get_contents($osrmUrl, false, $context);
$data = json_decode($res, true);
if (isset($data['routes'][0]['distance'])) {
    echo "OSRM distance: " . ($data['routes'][0]['distance'] / 1000) . " km\n";
    echo "OSRM geometry: " . json_encode($data['routes'][0]['geometry']) . "\n";
} else {
    echo "OSRM failed: " . print_r($data, true) . "\n";
}
exit;
