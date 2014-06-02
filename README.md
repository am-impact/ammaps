# Am Maps plugin for Craft CMS

## How does it look in the backend?

This plugin will add a new fieldtype called "Geo Mapper" that'll allow you to easily save addresses combined with the latitude and longitude.

![New field](resources/images/new-field.jpg "Geo Mapper")

Once you've added an address in the given fields and press the button to get the coordinates, Google Maps will be shown and a marker will display the location.

![Data in field](resources/images/data-in-field.jpg "Data in Geo Mapper field")

You can drag the marker around to update the coordinates to pinpoint the location exactly where you want.

![Drag the marker](resources/images/drag-for-coords.png "Drag the marker around in Geo Mapper field")

## How do I display the information on the frontend?

    <p>{{ entry.GeoMapperFieldName.address }}
    <p>{{ entry.GeoMapperFieldName.zip }}
    <p>{{ entry.GeoMapperFieldName.city }}
    <p>{{ entry.GeoMapperFieldName.country }}
    <p>{{ entry.GeoMapperFieldName.lat }}
    <p>{{ entry.GeoMapperFieldName.lng }}

## Contact

If you have any questions or suggestions, don't hesitate to contact us.
