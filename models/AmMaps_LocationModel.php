<?php
namespace Craft;

class AmMaps_LocationModel extends BaseModel
{
    protected function defineAttributes()
    {

        $coordsColumn = array(
            AttributeType::Number,
            'column'   => ColumnType::Decimal,
            'length'   => 12,
            'decimals' => 8
        );

        return array(
            'elementId' => AttributeType::Number,
            'handle'    => AttributeType::String,
            'address'   => AttributeType::String,
            'zip'       => AttributeType::String,
            'city'      => AttributeType::String,
            'country'   => AttributeType::String,
            'lat'       => $coordsColumn,
            'lng'       => $coordsColumn
        );
    }
}