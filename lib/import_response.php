<?php

namespace gpx_lib;

class import_response {

    public $error;
    public $waypoints;
    public $route_name;
    public $routepoints;

    public function __construct() {
        $this->error = '';
        $this->route_name = '';
        $this->waypoints = [];
        $this->routepoints = [];
    }

}

class routepoint {

    public $lat;
    public $lng;

    public function __construct($_lat, $_lng) {
        $this->lat = $_lat;
        $this->lng = $_lng;
    }

}

class waypoint {

    public $name;
    public $desc;
    public $lat;
    public $lng;

    public function __construct($_name, $_desc, $_lat, $_lng) {
        $this->name = $_name;
        $this->desc = $_desc;
        $this->lat = $_lat;
        $this->lng = $_lng;
    }

}
