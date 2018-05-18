<?php

header('Content-type: application/json');

$waypoints = $_POST['waypoints'];
$route_name = filter_input(INPUT_POST, 'routename', FILTER_DEFAULT);

$date = new DateTime('now', new DateTimeZone('UTC'));
$time = $date->format('c');

$waypoints_output = '';
$routepoints_output = '';

for ($i = 0; $i < count($waypoints); $i++) {
    if ($waypoints[$i]['pointType'] === 'wp') {
        if ($waypoints_output !== '') {
            $waypoints_output .= "\n";
        }
        
        $waypoints_output .= sprintf("<wpt lat=\"%s\" lon=\"%s\">
        <name>%s</name>
        <desc>%s</desc>
    </wpt>", trim($waypoints[$i]['lat']), trim($waypoints[$i]['lng']), trim($waypoints[$i]['name']), $waypoints[$i]['desc']);
        
    } else {
        if ($routepoints_output !== '') {
            $routepoints_output .= "\n\t\t";
        }
        
        $routepoints_output .= sprintf("<rtept lat=\"%s\" lon=\"%s\"/>", $waypoints[$i]['lat'], $waypoints[$i]['lng']);
    }
}

$output = sprintf("<?xml version=\"1.0\"?>\n<gpx version=\"1.1\" creator=\"gpxbuilder.com\">
    <metadata>
        <name>%s</name>
        <link href=\"http://www.gpxbuilder.com/\">
            <text>GPX Builder</text>
        </link>
    </metadata>
    %s
    %s
</gpx>", $route_name, $waypoints_output, ($routepoints_output !== '') ? sprintf("<rte>
        <name>%s</name>
        <src>GPX Builder</src>
                %s
    </rte>", $route_name, $routepoints_output) : "");


echo json_encode(array('gpx' => $output));

function sanitize_name($input) {
    // $input = str_replace(' ', '-', $input); // Replaces all spaces with hyphens.
    return trim(preg_replace('/[^A-Za-z0-9 \-]/', '', $input)); // Removes special chars.
}
