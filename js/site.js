var gpx = {};
gpx.routepoints = [];
gpx.isValid = true;
gpx.colorInvalidLabels = false;

gpx.init = function () {
    jQuery('#routepoints-list').sortable({
        create: function (events, ui) {
            gpx.updateMap();
            gpx.doAjax();
        },
        update: function (event, ui) {
            gpx.updateMap();
        }
    });

    jQuery('#add-routepoint-button').click(function () {
        jQuery('#routepoints-list').append(jQuery('#routepoint-item-template').html());
        gpx.initEvents();
    });

    jQuery('#add-waypoint-button').click(function () {
        jQuery('#waypoints-list').append(jQuery('#waypoint-item-template').html());
        gpx.initEvents();
    });
    gpx.initEvents();
    gpx.ajaxListener();
};

gpx.initEvents = function () {
    gpx.initRemoveRoutepointClickEvent();
    gpx.initUpdateEventListener();
    gpx.initOrderingButtonsListeners();
    gpx.initClearRoutepointsListener();
};

gpx.initRemoveRoutepointClickEvent = function () {
    jQuery('.waypoint-remove-button').click(function () {
        jQuery(this).parent().parents('li').remove();
        gpx.updateMap();
    });
};

gpx.initUpdateEventListener = function () {
    jQuery('#routepoints-list').find('input[type="text"], input[type="number"], textarea').change(function () {
        gpx.updateMap();
    });
    jQuery('#waypoints-list').find('input[type="text"], input[type="number"], textarea').change(function () {
        gpx.updateMap();
    });
};

gpx.initOrderingButtonsListeners = function () {
    jQuery('.glyphicon-chevron-up').click(function () {
        var item = jQuery(this).closest('li');
        if (jQuery('#routepoints-list').children('li').index(item) !== 0) {
            // item.prev().before(item);
            var content = item.prev();
            gpx.moveUpAnimation(item, content);
        }
    });
    jQuery('.glyphicon-chevron-down').click(function () {
        var item = jQuery(this).closest('li');
        var listItems = jQuery('#routepoints-list').children('li');
        if (listItems.index(item) !== listItems.length - 1) {
            var content = item.next();
            gpx.moveDownAnimation(item, content);
        }
    });
};

gpx.moveUpAnimation = function (item, content) {
    jQuery(item).hide('fade', 'swing', 400, function () {
        jQuery(content).before(item);
        jQuery(item).show('fade', 'swing', 400);
        gpx.updateMap();
    });
};

gpx.moveDownAnimation = function (item, content) {
    jQuery(item).hide('fade', 'swing', 400, function () {
        jQuery(content).after(item);
        jQuery(item).show('fade', 'swing', 400);
        gpx.updateMap();
    });
};

gpx.updateMap = function () {
    gpx.getAndValidateRoutepoints();
    if (gpx.isValid === true) {
        var infowindow = new google.maps.InfoWindow({
            maxWidth: 350
        });
        var bounds = new google.maps.LatLngBounds();
        var map = new google.maps.Map(document.getElementById('mapdiv'),
                {
                    styles: [{"elementType": "geometry", "stylers": [{"hue": "#ff4400"}, {"saturation": -68}, {"lightness": -4}, {"gamma": 0.72}]}, {"featureType": "road", "elementType": "labels.icon"}, {"featureType": "landscape.man_made", "elementType": "geometry", "stylers": [{"hue": "#0077ff"}, {"gamma": 3.1}]}, {"featureType": "water", "stylers": [{"hue": "#00ccff"}, {"gamma": 0.44}, {"saturation": -33}]}, {"featureType": "poi.park", "stylers": [{"hue": "#44ff00"}, {"saturation": -23}]}, {"featureType": "water", "elementType": "labels.text.fill", "stylers": [{"hue": "#007fff"}, {"gamma": 0.77}, {"saturation": 65}, {"lightness": 99}]}, {"featureType": "water", "elementType": "labels.text.stroke", "stylers": [{"gamma": 0.11}, {"weight": 5.6}, {"saturation": 99}, {"hue": "#0091ff"}, {"lightness": -86}]}, {"featureType": "transit.line", "elementType": "geometry", "stylers": [{"lightness": -48}, {"hue": "#ff5e00"}, {"gamma": 1.2}, {"saturation": -23}]}, {"featureType": "transit", "elementType": "labels.text.stroke", "stylers": [{"saturation": -64}, {"hue": "#ff9100"}, {"lightness": 16}, {"gamma": 0.47}, {"weight": 2.7}]}]
                });

        var locations = [];

        for (var i = 0; i < gpx.routepoints.length; i++) {

            if (gpx.routepoints[i].pointType === 'rt') {

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(gpx.routepoints[i].lat, gpx.routepoints[i].lng),
                    label: String(i + 1),
                    animation: google.maps.Animation.DROP,
                    map: map
                });

                bounds.extend(marker.position);
                locations.push(marker.position);

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infowindow.setContent('<h4>RT ' + (i + 1) + '</h4> <span style="font-weight: bold">' + gpx.routepoints[i].lat + ', ' + gpx.routepoints[i].lng + '</span>');
                        infowindow.open(map, marker);
                    };
                })(marker, i));

            } else if (gpx.routepoints[i].pointType === 'wp') {
                
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(gpx.routepoints[i].lat, gpx.routepoints[i].lng),
                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    animation: google.maps.Animation.DROP,
                    map: map
                });
                
                bounds.extend(marker.position);
                
                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infowindow.setContent('<h4>' + gpx.routepoints[i].name + '</h4> <span style="font-weight: bold">' + gpx.routepoints[i].lat + ', ' + gpx.routepoints[i].lng + '</span> <p>' + gpx.routepoints[i].desc + '</p>');
                        infowindow.open(map, marker);
                    };
                })(marker, i));
            }

        }

        if (locations.length > 1) {
            var route = new google.maps.Polyline({
                path: locations,
                geodesic: true,
                icons: [{
                        icon: {
                            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                            scale: 3,
                            strokeColor: '#393',
                            fillColor: '#393',
                            fillOpacity: 1
                        },
                        offset: '100%'
                    }],
                strokeColor: '#333',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            route.setMap(map);
            gpx.animateCircle(route);
        }

        if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
            var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.0001, bounds.getNorthEast().lng() + 0.0001);
            var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.0001, bounds.getNorthEast().lng() - 0.0001);
            bounds.extend(extendPoint1);
            bounds.extend(extendPoint2);
        }

        map.fitBounds(bounds);
    }
};

gpx.animateCircle = function (line) {
    var count = 0;
    window.setInterval(function () {
        count = (count + 1) % 200;

        var icons = line.get('icons');
        icons[0].offset = (count / 2) + '%';
        line.set('icons', icons);
    }, 20);
};

gpx.ajaxListener = function () {
    jQuery('#build-gpx').click(function () {
        gpx.doAjax();
    });
};

gpx.doAjax = function () {
    gpx.routepoints = [];
    gpx.getAndValidateRoutepoints();
    if (gpx.isValid === true) {
        jQuery.ajax({
            url: '/handler.php',
            method: 'POST',
            data: {
                type: jQuery('#output-type').val(),
                waypoints: gpx.routepoints,
                routename: jQuery('#route-name').val()
            }
        }).success(function (data) {
            jQuery('.gpx-output').val(data.gpx);
        });
    }
};

gpx.getAndValidateRoutepoints = function () {
    gpx.routepoints = [];
    gpx.isValid = true;

    var elements = jQuery('#routepoints-list').children();
    for (var i = 0; i < elements.length; i++) {
        gpx.routepoints.push({
            lat: gpx.validateSingleField(jQuery(elements[i]).find('.lat')),
            lng: gpx.validateSingleField(jQuery(elements[i]).find('.lng')),
            pointType: 'rt'
        });
    }

    var elements = jQuery('#waypoints-list').children();
    for (var i = 0; i < elements.length; i++) {
        gpx.routepoints.push({
            name: gpx.validateSingleField(jQuery(elements[i]).find('.waypoint-name')),
            lat: gpx.validateSingleField(jQuery(elements[i]).find('.lat')),
            lng: gpx.validateSingleField(jQuery(elements[i]).find('.lng')),
            desc: gpx.validateSingleField(jQuery(elements[i]).find('.desc')),
            pointType: 'wp'
        });
    }
};

gpx.validateSingleField = function (element) {
    var val = jQuery(element).val();
    if (!val && jQuery(element).hasClass('required')) {
        gpx.isValid = false;
        if (gpx.colorInvalidLabels)
            jQuery(element).siblings('label').css('color', 'red');
        return '';
    }
    if (gpx.colorInvalidLabels === true)
        jQuery(element).siblings('label').css('color', '#333333');
    return val;
};

gpx.initClearRoutepointsListener = function () {
    jQuery('#clear-routepoints-button').click(function () {
        var routepointsListItems = jQuery('#routepoints-list').children();
        for (var i = 0; i < routepointsListItems.length; i++) {
            if (i === 0)
                continue;
            jQuery(routepointsListItems[i]).remove();
        }
        var inputs = jQuery(routepointsListItems[0]).find('input');
        for (var j = 0; j < inputs.length; j++) {
            jQuery(inputs[j]).attr('value', '');
        }
        
        jQuery('.gpx-output').val('');
//        var map = new google.maps.Map(document.getElementById('mapdiv'),
//                {
//                    styles: gpx.mapStyles
//                });
                
        gpx.updateMap();
    });

    jQuery('#clear-waypoints-button').click(function() {
        var waypointsListItems = jQuery('#waypoints-list').children();
        for (var i = 0; i < waypointsListItems.length; i++) {
            if (i === 0)
                continue;
            jQuery(waypointsListItems[i]).remove();
        }

        var inputs = jQuery(waypointsListItems[0]).find('input');
        for (var j = 0; j < inputs.length; j++) {
            jQuery(inputs[j]).attr('value', '');
        }

        var descriptions = jQuery(waypointsListItems[0]).find('textarea');
        for (var k = 0; k < descriptions.length; k++) {
            jQuery(descriptions[k]).html('');
        }

        jQuery('.gpx-output').val('');
        gpx.updateMap();
    });

};

jQuery(document).ready(function () {
    gpx.init();
    new Clipboard('#copy-output');
});