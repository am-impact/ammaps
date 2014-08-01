<?php
namespace Craft;

class AmMaps_GeoMapperFieldType extends BaseFieldType
{
    public function getName()
    {
        return 'Geo Mapper';
    }

    public function modifyElementsQuery(DbCommand $query, $params)
    {
        if ($params !== null) {
            craft()->amMaps->modifyElementsQuery($query, $params);
        }
    }

    public function getInputHtml($name, $value)
    {
        // Load resources
        $js = '
        new Craft.GeoMapper({
            handle: "' . $name . '"
        });';
        craft()->templates->includeJsFile('//maps.google.com/maps/api/js?v=3&amp;sensor=false');
        craft()->templates->includeJsResource('ammaps/js/GeoMapper.js');
        craft()->templates->includeJs($js);
        craft()->templates->includeCssResource('ammaps/css/GeoMapper.css');
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
     * Modify input value before it's parsed to the template.
     *
     * - Will be triggered before Craft will render the template.
     *
     * @param mixed $value Value from Craft's elements table, which we will ignore because of our own table.
     *
     * @return type
     */
    public function prepValue($value)
    {
        return craft()->amMaps->getGeoMapperData($this);
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