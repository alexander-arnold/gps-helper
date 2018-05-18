<?php

namespace gpx_lib;

require_once 'import_response.php';

class helper {

    public static function verify_request() {
        try {
            return ($_SESSION['gpx_key'] === $_POST['gpx_key']);
        } catch (Exception $exc) {
            return false;
        }
    }

    public static function generate_nonce($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char_length = strlen($characters);
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, $char_length - 1)];
        }
        return $random_string;
    }

    public static function validate_request() {
        if (count($_FILES) !== 1 || !isset($_FILES['file_import']) || $_FILES['file_import']['error'] == 4)
            return 'No file uploaded';

        if ($_FILES['file_import']['error'] == 1 || $_FILES['file_import']['error'] == 2)
            return 'File exceeds maximum allowed file size';

        $allowed = array('gpx');
        $filename = $_FILES['file_import']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed))
            return 'File must be gpx';

        return '';
    }

    public static function get_gpx_data() {
        try {
            $data = new \SimpleXMLElement(file_get_contents($_FILES['file_import']['tmp_name']), LIBXML_NOCDATA);
            $response = new \gpx_lib\import_response();
            
            if (isset($data->wpt)) {
                foreach ($data->wpt as $wpt) {
                    array_push($response->waypoints, new \gpx_lib\waypoint($wpt->name->__toString(), $wpt->desc->__toString(), (string)$wpt['lat'], (string)$wpt['lon']));
                }
            }
            
            if (isset($data->rte) && isset($data->rte->rtept)) {
                foreach ($data->rte->rtept as $p) {
                    array_push($response->routepoints, new \gpx_lib\routepoint((string)$p['lat'], (string)$p['lon']));
                }
            }
            
            return $response;
            
//            print_r($response);
//            die();
        } catch (Exception $exc) {
            $response = new \gpx_lib\import_response();
            $response->error = $exc;
            return $response;
        }
    }

}
