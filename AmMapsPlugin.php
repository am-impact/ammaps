<?php
/**
 * Google Maps for Craft CMS
 *
 * @package   Am Maps
 * @author    Hubert Prein
 */
namespace Craft;

class AmMapsPlugin extends BasePlugin
{
    public function getName()
    {
         return 'a&m maps';
    }

    public function getVersion()
    {
        return '1.1.3';
    }

    public function getDeveloper()
    {
        return 'a&m impact';
    }

    public function getDeveloperUrl()
    {
        return 'http://www.am-impact.nl';
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('ammaps/settings', array(
            'settings' => $this->getSettings()
        ));
    }

    protected function defineSettings()
    {
        return array(
            'apiKey' => array(AttributeType::String, 'default' => ''),
        );
    }
}
