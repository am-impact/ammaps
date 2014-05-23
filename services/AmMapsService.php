<?php
namespace Craft;

class AmMapsService extends BaseApplicationComponent
{
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
        $attr = $content->getAttribute($handle);

        print_r($attr);

        die();
	}
}