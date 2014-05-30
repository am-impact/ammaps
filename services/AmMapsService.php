<?php
namespace Craft;

class AmMapsService extends BaseApplicationComponent
{
    /**
     * Get Geo Mapper field data.
     *
     * @param BaseFieldType $fieldType
     *
     * @return array
     */
    public function getGeoMapperData(BaseFieldType $fieldType)
    {
        $geoMapperRecord = AmMaps_GeoMapperRecord::model()->findByAttributes(array(
            'elementId' => $fieldType->element->id,
            'handle'    => $fieldType->model->handle
        ));
        // Get attributes
        $attributes = array();
        if($geoMapperRecord)
        {
            $attributes = $geoMapperRecord->getAttributes();
        }
        return $attributes;
    }

	/**
	 * Save Geo Maper field to the database.
	 *
	 * @param BaseFieldType $fieldType
	 *
	 * @return bool
	 */
	public function saveGeoMapperField(BaseFieldType $fieldType)
	{
		// Get handle, elementId, and content
        $handle    = $fieldType->model->handle;
        $elementId = $fieldType->element->id;
        $content   = $fieldType->element->getContent();
        // Set specified attributes
        if(($attributes = $content->getAttribute($handle)) === false)
        {
        	return false; // Attributes don't exist
        }
        // Attempt to load existing record
        $geoMapperRecord = AmMaps_GeoMapperRecord::model()->findByAttributes(array(
            'elementId' => $elementId,
            'handle'    => $handle
        ));
        // If no record exists, create new record
        if(! $geoMapperRecord) {
            $geoMapperRecord = new AmMaps_GeoMapperRecord;
            $attributes['elementId'] = $elementId;
            $attributes['handle']    = $handle;
        }
        // Set default values
        foreach($attributes as $key => $value)
        {
            if(! $value)
            {
                $attributes[$key] = null;
            }
        }
        // Set record values
        $geoMapperRecord->setAttributes($attributes, false);
        // Save in database
        return $geoMapperRecord->save();
	}
}