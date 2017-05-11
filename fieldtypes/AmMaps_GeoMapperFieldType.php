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

    public function getSettingsHtml()
    {
        return craft()->templates->render('ammaps/geomapper/settings', array(
            'settings' => $this->getSettings()
        ));
    }

    public function getInputHtml($name, $value)
    {
        // Plugin settings
        $apiKey = '';
        $plugin = craft()->plugins->getPlugin('ammaps');
        if ($plugin) {
            $pluginSettings = $plugin->getSettings();
            $apiKey = $pluginSettings->apiKey;
        }

        // Field settings
        $settings = $this->getSettings();

        // Load resources
        $js = '
        new Craft.GeoMapper({
            handle: "' . $name . '",
            seperatedAddress: ' . ($settings->seperatedAddress ? 'true' : 'false') . '
        });';
        craft()->templates->includeJsFile('//maps.google.com/maps/api/js?v=3&amp;key=' . $apiKey);
        craft()->templates->includeJsResource('ammaps/js/GeoMapper.min.js');
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

        // Render template
        $variables = array(
            'field'    => $locationModel->getAttributes(),
            'settings' => $settings
        );
        return craft()->templates->render('ammaps/geomapper/input', $variables);
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
     * @param mixed $value
     *
     * @return mixed
     */
    public function prepValueFromPost($value)
    {
        if ($this->element->id) {
            return craft()->amMaps->saveGeoMapperField($this, $value);
        }

        return $value;
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

    /**
     * Fieldtype settings.
     *
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'seperatedAddress' => array(AttributeType::Bool, 'default' => false)
        );
    }
}
