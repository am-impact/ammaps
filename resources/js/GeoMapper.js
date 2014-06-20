(function($) {

Craft.GeoMapper = Garnish.Base.extend({
    handle: null,
    $container: null,
    $mapsContainer: null,
    $inputFields: null,
    $addressField: null,
    $buttonField: null,
    $latField: null,
    $lngField: null,
    googleMaps: null,
    googleMarker: null,
    googleGeo: new google.maps.Geocoder,

	init: function(params) {
        // Set variables
        this.handle = params.handle;
		this.$container = $('.geo-mapper-field.' + this.handle);
        this.$mapsContainer = $('.geo-mapper-maps', this.$container);
        this.$inputFields = this.$container.find('input:not(.geo-mapper-ignore)');
        this.$addressField = this.$container.find('input[name*="address"]');
        this.$buttonField = this.$container.find('input[name*="updateCoords"]');
        this.$latField = this.$container.find('input[name*="lat"]');
        this.$lngField = this.$container.find('input[name*="lng"]');
        // Add listener
        this.addListener(this.$buttonField, 'click', 'getCoords');
        // Do we have saved data?
        var lat = this.$latField.val(),
            lng = this.$lngField.val();
        if(lat != '' && lng != '') {
            var location = new google.maps.LatLng(lat, lng);
            this.createMap(this, location);
        }
	},

    /**
     * Get coordinates based on values from input fields.
     *
     * @param object event Triggered event.
     */
    getCoords: function(event) {
        // Get the required input values
        var inputs = {},
            self = this,
            doUpdate = false;
        $.each(this.$inputFields, function(i, field) {
            var fieldName = $(field).attr('id').replace('fields-' + self.handle + '-', '');
            inputs[fieldName] = $(field).val();
            if(inputs[fieldName] != '') {
                doUpdate = true;
            }
        });
        // We only update if we have at least one set value
        if(doUpdate) {
            var geoAddress = inputs.address;
            geoAddress += inputs.zip ? ', ' + inputs.zip : '';
            geoAddress += inputs.city ? ', ' + inputs.city : '';
            geoAddress += inputs.country ? ', ' + inputs.country : '';
            this.getGeoLocation(geoAddress, this.updateMap);
        }
        event.preventDefault();
	},

    /**
     * Display and create a Google Maps object.
     *
     * @param object self     Geo Mapper object.
     * @param object location Google Maps Lat and Lng object.
     */
    createMap: function(self, location)
    {
        self.$mapsContainer.fadeIn(400, function() {
            self.googleMaps = new google.maps.Map(self.$mapsContainer[0], {
                zoom: 1,
                scrollwheel: false,
                center: location,
                streetViewControl: false
            });
            self.updateMap(self, location);
        });
    },

    /**
     * Update the location on Google Maps.
     *
     * @param object self     Geo Mapper object.
     * @param object location Google Maps Lat and Lng object.
     */
    updateMap: function(self, location) {
        // Handle visibility
        if(! self.$mapsContainer.is(':visible'))
        {
            // We will end this function and update after google maps is visible and loaded
            self.createMap(self, location);
            return;
        }
        // Handle marker
        if(self.googleMarker === null) {
            // Create the marker
            self.googleMarker = new google.maps.Marker({
                map: self.googleMaps,
                draggable: true,
                animation: google.maps.Animation.DROP,
                position: location
            });
            // Handle coords after marker dragging
            google.maps.event.addListener(self.googleMarker, 'dragend', function(event) {
                self.updateCoordFields(self, event, true);
            });
        }
        else {
            self.googleMarker.setPosition(location);
        }
        // Handle map
        self.googleMaps.setCenter(location);
        if(self.googleMaps.getZoom() < 15) {
            self.googleMaps.setZoom(15);
        }
        // Handle coords
        self.updateCoordFields(self, location, false);
    },

    /**
     * Update the coordinates input fields.
     *
     * @param object self        Geo Mapper object.
     * @param object location    Google Maps object.
     * @param bool   eventUpdate Which type triggered the function.
     */
    updateCoordFields: function(self, location, eventUpdate) {
        if(eventUpdate) {
            self.$latField.val(location.latLng.lat());
            self.$lngField.val(location.latLng.lng());
        }
        else {
            self.$latField.val(location.lat());
            self.$lngField.val(location.lng());
        }
    },

    /**
     * Save Geo Mapper field in the database through service.
     *
     * @param string   address  The address to get the lat and lng for.
     * @param function callback The function that'll be triggered after the location has been found.
     */
    getGeoLocation: function(address, callback) {
        var self = this,
            location = null;
        this.googleGeo.geocode({'address': address}, function(results, status) {
            if(status == google.maps.GeocoderStatus.OK) {
                location = results[0].geometry.location;
                callback(self, location);
            }
        });
    }
});

})(jQuery);