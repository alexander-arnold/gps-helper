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

    public function toHtml() {
        ?>
        <li>
            <table>
                <tr>
                    <td>
                        <span class="glyphicon glyphicon-chevron-up"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </td>
                    <td>
                        <ul class="list-inline waypoints-list-item-field-group">
                            <li>
                                <label class="label required">Latitude</label>
                                <input type="text" class="form-control required lat" value="<?php echo $this->lat; ?>" />
                            </li>
                            <li>
                                <label class="label required">Longitude</label>
                                <input type="text" class="form-control required lng" value="<?php echo $this->lng; ?>" />
                            </li>
                            <li>
                                <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
        <?php
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

    public function toHtml() {
        ?>
        <li>
            <table>
                <tr>
                    <td>
                        <span class="glyphicon glyphicon-chevron-up"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </td>
                    <td>
                        <ul class="list-inline waypoints-list-item-field-group">
                            <li class="waypoint-name-list-item">
                                <label class="label required">Name</label>
                                <input type="text" class="form-control required waypoint-name" value="<?php echo $this->name; ?>" />
                            </li>
                            <li>
                                <label class="label required">Latitude</label>
                                <input type="text" class="form-control required lat" value="<?php echo $this->lat; ?>" />
                            </li>
                            <li>
                                <label class="label required">Longitude</label>
                                <input type="text" class="form-control required lng" value="<?php echo $this->lng; ?>" />
                            </li>
                            <li class="waypoint-description-list-item">
                                <label class="label">Description</label>
                                <textarea class="form-control desc"><?php echo $this->desc; ?></textarea>
                            </li>
                            <li class="waypoint-remove-button-list-item">
                                <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
        <?php
    }

}
