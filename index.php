<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/lib/helper.php';

$errors = '';
$gpx_data = false;

if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && isset($_POST['gpx_submit'])) {
    if (!gpx_lib\helper::verify_request())
        throw new Exception('invalid request');

    $errors = gpx_lib\helper::validate_request();
    $gpx_data = gpx_lib\helper::get_gpx_data();
    $alex = '';
}

$nonce = gpx_lib\helper::generate_nonce();
$_SESSION['gpx_key'] = $nonce;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GPX Builder</title>
        <meta name="description" content="GPX waypoint and route file generation for handheld GPS units">
        <meta name="keywords" content="GPX,GPX File Generator,GPX File,GPX FILE Builder,GPX Waypoints,GPS Route">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/site.css?v=2" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-96180501-1', 'auto');
            ga('send', 'pageview');

        </script>
    </head>
    <body>
        <ul id="waypoint-item-template" style="display: none;">
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
                                    <input type="text" class="form-control required waypoint-name" />
                                </li>
                                <li>
                                    <label class="label required">Latitude</label>
                                    <input type="text" class="form-control required lat" />
                                </li>
                                <li>
                                    <label class="label required">Longitude</label>
                                    <input type="text" class="form-control required lng" />
                                </li>
                                <li class="waypoint-description-list-item">
                                    <label class="label">Description</label>
                                    <textarea class="form-control desc"></textarea>
                                </li>
                                <li class="waypoint-remove-button-list-item">
                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </li>
        </ul>
        <ul id="routepoint-item-template" style="display: none;">
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
                                    <input type="text" class="form-control required lat" />
                                </li>
                                <li>
                                    <label class="label required">Longitude</label>
                                    <input type="text" class="form-control required lng" />
                                </li>
                                <li>
                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </li>
        </ul>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#top">GPX Builder</a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="#mapdiv">Map</a>
                        </li>
                        <li>
                            <a href="#output">GPX Output</a>
                        </li>
                        <!--                        <li>
                                                    <a href="#calculator">Calculator</a>
                                                </li>-->
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="hero-unit">
                        <h1 id="top">GPX Builder</h1>
                        <!--<p>GPX waypoint and route file builder. Add waypoints and save the output as a .gpx file for uploading to handheld GPS units. More features to come soon!</p>-->
                    </div>
                </div>
                <div class="col-lg-6">
                    <h3 class="h3">Waypoints</h3>
                    <ul class="list-unstyled waypoints-list" id="waypoints-list">
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
                                                <input type="text" class="form-control required waypoint-name" value="North Fork Bouquet Trailhead" />
                                            </li>
                                            <li>
                                                <label class="label required">Latitude</label>
                                                <input type="text" class="form-control required lat" value="44.1122246" />
                                            </li>
                                            <li>
                                                <label class="label required">Longitude</label>
                                                <input type="text" class="form-control required lng" value="-73.707608" />
                                            </li>
                                            <li class="waypoint-description-list-item">
                                                <label class="label">Description</label>
                                                <textarea class="form-control desc">Park on the side of the road here wherever you can find a safe spot just east of the bridge over the river.  You'll be looking for a herdpath into the woods heading south.  As you follow the herdpath through the woods you will pass by a bunch of really great swimming holes along the North Fork Bouquet.  Start out on the trail on the east side of the river.  After a half mile you'll cross over to the west side of the river which is the herdpath you will follow.</textarea>
                                            </li>
                                            <li class="waypoint-remove-button-list-item">
                                                <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </li>
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
                                                <input type="text" class="form-control required waypoint-name" value="River Crossings" />
                                            </li>
                                            <li>
                                                <label class="label required">Latitude</label>
                                                <input type="text" class="form-control required lat" value="44.1100214" />
                                            </li>
                                            <li>
                                                <label class="label required">Longitude</label>
                                                <input type="text" class="form-control required lng" value="-73.7189054" />
                                            </li>
                                            <li class="waypoint-description-list-item">
                                                <label class="label">Description</label>
                                                <textarea class="form-control desc">The herd path at this point will begin to cross several streams in the area.  Be careful rock-hopping and make sure to look for cairns (small piles of rocks) to guide your way.</textarea>
                                            </li>
                                            <li class="waypoint-remove-button-list-item">
                                                <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    </ul>
                    <ul class="list-inline">
                        <li>
                            <button type="button" class="btn btn-primary" id="add-waypoint-button">Add Waypoint</button>
                        </li>
                    </ul>
                    <ul class="list-inline">
                        <li>
                            <button type="button" class="btn btn-secondary" id="clear-waypoints-button">Clear Waypoints</button>
                        </li>
                    </ul>
                    <hr />
                    <h3 class="h3">Route Points</h3>
                    <label class="label">Route Name</label>
                    <input type="text" class="form-control" id="route-name" />
                    <ul class="list-unstyled waypoints-list" id="routepoints-list">
                        <?php
                        if ($gpx_data === false) {
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
                                                    <input type="text" class="form-control required lat" value="44.11222" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.70761" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11242" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.70838" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11253" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.70871" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11271" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.70913" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11227" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.71131" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11185" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.7114" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11122" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.7124" />
                                                </li>
                                                <li>
                                                    <button type="button" class="btn btn-primary waypoint-remove-button">Remove</button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </li>
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
                                                    <input type="text" class="form-control required lat" value="44.11075" />
                                                </li>
                                                <li>
                                                    <label class="label required">Longitude</label>
                                                    <input type="text" class="form-control required lng" value="-73.71397" />
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
                        } else {
                            foreach ($gpx_data->routepoints as $rp) {
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
                                                        <input type="text" class="form-control required lat" value="<?php echo $rp->lat; ?>" />
                                                    </li>
                                                    <li>
                                                        <label class="label required">Longitude</label>
                                                        <input type="text" class="form-control required lng" value="<?php echo $rp->lng; ?>" />
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
                        ?>
                    </ul>
                    <ul class="list-inline">
                        <li>
                            <button type="button" class="btn btn-primary" id="add-routepoint-button">Add Route Point</button>
                        </li>
                    </ul>
                    <ul class="list-inline">
                        <li>
                            <button type="button" class="btn btn-secondary" id="clear-routepoints-button">Clear Route Points</button>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <h3 class="h3">Map View</h3>
                    <div id="mapdiv"></div>
                    <h3 class="h3">GPX File Import</h3>
                    <span style="color: red; font-weight: bold;"><?php echo $errors; ?></span>
                    <form method="post" action="" enctype="multipart/form-data" style="border: solid #eeeeee 1px; padding: 1em; border-radius: 1em; background-color: #cccccc;">
                        <input style="display: none;" type="text" value="<?php echo $nonce; ?>" name="gpx_key" />
                        <ul class="list-unstyled">
                            <li style="margin-bottom: 1em;">
                                <label for="gpx-import">GPX File</label>
                                <input type="file" id="gpx-import" name="file_import" />
                            </li>
                            <li>
                                <button class="btn btn-primary" type="submit" name="gpx_submit">Run Import</button>
                            </li>
                        </ul>
                    </form>
                    <hr />
                    <h3 class="h3" id="output">GPX File Output</h3>
                    <p class="text-left">Copy and paste the output below into a file with a .gpx extension. If output type is route then be sure to fill out the route's name tag so it is easy to find on a GPS.</p>
                    <!--                    <label for="output-type">Output Type</label>
                                        <select id="output-type" class="form-control" style="margin-bottom: 1em;">
                                            <option value="wp">Waypoints</option>
                                            <option value="rt">Route</option>
                                        </select>-->
                    <!--                    <div class="route-name-input-wrapper" style="display: none;">
                                            <label for="route-name">Route Name</label>
                                            <input type="text" id="route-name" class="form-control" style="margin-bottom: 1em;" />
                                        </div>-->
                    <ul class="list-inline">
                        <li>
                            <button type="button" class="btn btn-primary" id="build-gpx" style="display: block; margin-bottom: 1em;">Build GPX</button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-secondary" id="copy-output" data-clipboard-target=".gpx-output">Copy Output</button>
                        </li>
                    </ul>
                    <textarea class="gpx-output" rows="12" readonly></textarea>
                </div>
            </div>
        </div>
        <footer class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p style="text-align: center;">GPX Builder <?php echo date('Y'); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p style="text-align: center;"><a href="mailto:alex@gpxbuilder.com">alex@gpxbuilder.com</a></p>
                </div>
            </div>
        </footer>
        <script src="js/jquery.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAy5szVN73uImKDY2Oa5d8JeiP1XF_BkNM"></script>
        <script src="js/clipboard.min.js"></script>
        <script src="js/site.js?v=2"></script>
    </body>
</html>
