<?php
namespace Craft;

class AmMaps_GeoMapperFieldType extends BaseFieldType
{
    public function getName()
    {
        return 'Geo Mapper';
    }

    public function getInputHtml($name, $value)
    {
        // Load resources
        craft()->templates->includeCssResource('ammaps/css/fieldtype.css');

        // Set model
        if (!empty($value))
        {
            $locationModel = AmMaps_LocationModel::populateModel($value);
        }
        else
        {
            $locationModel = new AmMaps_LocationModel;
            $locationModel->handle = $name;
        }

        return craft()->templates->render('ammaps/geomapper/input', $locationModel->getAttributes());
    }

    /**
     * Save Geo Mapper field in the database through service.
     *
     * - Will be triggered after Craft has stored the field information in it's own table.
     *
     * @return type
     */
    public function onAfterElementSave()
    {
        return craft()->amMaps->saveGeoMapperField($this);
    }
}