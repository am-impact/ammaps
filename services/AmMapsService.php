<?php
namespace Craft;

class AmMapsService extends BaseApplicationComponent
{
    /*
     * Modify the elements query.
     *
     */
    public function modifyElementsQuery(DbCommand $query, $params = array())
    {
        // Join with plugin table
        $query->join(AmMaps_GeoMapperRecord::TableName, 'elements.id=' . craft()->db->tablePrefix.AmMaps_GeoMapperRecord::TableName.'.elementId');
        // Prepare where statement
        $this->_searchParams($query, $params);
    }


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
            'handle'    => $fieldType->model->handle,
            'locale'    => $fieldType->element->locale
        ));
        // Get attributes for current locale
        $attributes = array();
        if ($geoMapperRecord) {
            $attributes = $geoMapperRecord->getAttributes();
        }
        else {
            // Find record for any locale
            $geoMapperRecord = AmMaps_GeoMapperRecord::model()->findByAttributes(array(
                'elementId' => $fieldType->element->id,
                'handle'    => $fieldType->model->handle
            ));
            if ($geoMapperRecord) {
                $attributes = $geoMapperRecord->getAttributes();
            }
        }
        return $attributes;
    }

    /**
     * Save Geo Maper field to the database.
     *
     * @param BaseFieldType $fieldType
     * @param array         $attributes
     *
     * @return bool
     */
    public function saveGeoMapperField(BaseFieldType $fieldType, $attributes = array())
    {
        // Get handle, elementId, locale and content
        $handle    = $fieldType->model->handle;
        $elementId = $fieldType->element->id;
        $locale    = $fieldType->element->locale;
        $content   = $fieldType->element->getContent();
        if (! count($attributes)) {
            // Set specified attributes
            if (($attributes = $content->getAttribute($handle)) === false) {
                return false; // Attributes don't exist
            }
            // When a global set stores the attributes in Field Layout, the attributes variable is an object all of sudden
            if (! is_array($attributes)) {
                $attributes = json_decode($attributes, true);
            }
            // Now that we know for sure that the attributes is an array, check if we have anything set at all
            if (! count($attributes)) {
                return false;
            }
        }
        if (! is_array($attributes) && is_string($attributes)) {
            $attributes = json_decode($attributes, true);
        }
        // Attempt to load existing record
        $geoMapperRecord = AmMaps_GeoMapperRecord::model()->findByAttributes(array(
            'elementId' => $elementId,
            'handle'    => $handle,
            'locale'    => $locale
        ));
        // If no record exists, create new record
        if (! $geoMapperRecord) {
            $geoMapperRecord = new AmMaps_GeoMapperRecord;
            $attributes['elementId'] = $elementId;
            $attributes['handle']    = $handle;
            $attributes['locale']    = $locale;
        }
        // Set default values
        if (is_array($attributes)) {
            $clearCoordFields = true;
            foreach($attributes as $key => $value) {
                if ($key != 'lat' && $key != 'lng' && ! empty($value)) {
                    $clearCoordFields = false;
                }
                if (! $value) {
                    $attributes[$key] = null;
                }
            }
            if ($clearCoordFields) {
                $attributes['lat'] = null;
                $attributes['lng'] = null;
            }
        }
        // Set record values
        $geoMapperRecord->setAttributes($attributes, false);
        // Save in database
        return $geoMapperRecord->save();
    }

    /**
     * Create a where statement for the given parameters and add it to the query.
     *
     * @param DbCommand $query
     * @param array     $params Params to apply to the query.
     */
    private function _searchParams(&$query, $params)
    {
        if ($params !== null && is_array($params)) {
            $tableName = craft()->db->tablePrefix . AmMaps_GeoMapperRecord::TableName;
            if (count($params)) {
                foreach($params as $key => $value) {
                    $query->andWhere(DbHelper::parseParam($tableName . '.' . $key, $params, $query->params));
                }
            }
        }
    }
}
