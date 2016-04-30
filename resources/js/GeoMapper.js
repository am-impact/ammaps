(function($) {

Craft.GeoMapper = Garnish.Base.extend({
    handle: null,
    $container: null,
    $mapsContainer: null,
    $fieldTab: null,
    $inputFields: null,
    $buttonField: null,
    $latField: null,
    $lngField: null,
    googleMaps: null,
    googleMarker: null,
    googleGeo: new google.maps.Geocoder,
    createdMap: false,
    skipFirstUpdate: false,
    seperatedAddress: false,

    init: function(params) {
        // Set variables
        this.handle = params.handle;
        this.seperatedAddress = params.seperatedAddress;
        this.$container = $('#' + this.handle);
        this.$mapsContainer = $('.geo-mapper-maps', this.$container);
        this.$fieldTab = this.$container.closest('.field').parent();
        this.$inputFields = this.$container.find('input:not(.geo-mapper-ignore)');
        this.$buttonField = this.$container.find('input[name*="updateCoords"]');
        this.$latField = this.$container.find('input[name*="lat"]');
        this.$lngField = this.$container.find('input[name*="lng"]');

        // Add listener
        this.addListener(this.$buttonField, 'click', 'getCoords');
        this.addListener($('.tabs .tab'), 'click', 'switchElementTab');

        // Init saved coords if the field is on the first tab or in globals (no tabs)
        if (this.$fieldTab.attr('id') == 'tab1' || this.$fieldTab.attr('id') == undefined) {
            this.initSavedCoords();
        }
    },

    /**
     * Initialize Google Maps based on saved coordinates.
     */
    initSavedCoords: function() {
        // Do we have saved data?
        var lat = this.$latField.val(),
            lng = this.$lngField.val();
        if(lat != '' && lng != '') {
            var location = new google.maps.LatLng(lat, lng);

            this.skipFirstUpdate = true;
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
            var fieldName = $(field).attr('id').substring($(field).attr('id').lastIndexOf("-")).substring(1);
            inputs[fieldName] = $(field).val();
            if(inputs[fieldName] != '') {
                doUpdate = true;
            }
        });
        // We only update if we have at least one set value
        if(doUpdate) {
            var geoAddress = self.seperatedAddress ? inputs.street + ' ' + inputs.housenumber : inputs.address;
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
        if (! this.createdMap) {
            this.createdMap = true;

            self.$mapsContainer.removeClass('hidden');
            self.$mapsContainer.fadeIn(400, function() {
                self.googleMaps = new google.maps.Map(self.$mapsContainer[0], {
                    zoom: 1,
                    scrollwheel: false,
                    center: location,
                    streetViewControl: false
                });
                self.updateMap(self, location);
            });
        }
    },

    /**
     * Update the location on Google Maps.
     *
     * @param object self     Geo Mapper object.
     * @param object location Google Maps Lat and Lng object.
     */
    updateMap: function(self, location) {
        // Handle visibility
        if(self.$mapsContainer.hasClass('hidden'))
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
        // If we have data available, don't update the fields in order to prevent Craft's "Any changes will be lost if you leave this page." message.
        if (! self.skipFirstUpdate) {
            if(eventUpdate) {
                self.$latField.val(location.latLng.lat());
                self.$lngField.val(location.latLng.lng());
            }
            else {
                self.$latField.val(location.lat());
                self.$lngField.val(location.lng());
            }
        }
        else {
            self.skipFirstUpdate = false;
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
    },

    /**
     * Triggers when you select another tab when creating / editing an Element.
     *
     * @param object event Triggered event.
     */
    switchElementTab: function(event) {
        if (! this.createdMap) {
            var tabId = $(event.currentTarget).attr('href').replace('#', ''),
                $tab = $('#' + tabId);

            if ($tab.attr('id') == this.$fieldTab.attr('id')) {
                this.initSavedCoords();
            }
        }
    }
});

})(jQuery);
