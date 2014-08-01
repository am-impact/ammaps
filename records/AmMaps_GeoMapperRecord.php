<?php
namespace Craft;

class AmMaps_GeoMapperRecord extends BaseRecord
{
    const TableName = 'ammaps_geomapper';
    public function getTableName()
    {
        return static::TableName;
    }

    protected function defineAttributes()
    {
        $coordsColumn = array(
            AttributeType::Number,
            'column'   => ColumnType::Decimal,
            'length'   => 12,
            'decimals' => 8
        );
        return array(
            'handle'    => AttributeType::String,
            'address'   => AttributeType::String,
            'zip'       => AttributeType::String,
            'city'      => AttributeType::String,
            'country'   => AttributeType::String,
            'lat'       => $coordsColumn,
            'lng'       => $coordsColumn
        );
    }

    public function defineRelations()
    {
        return array(
            'element' => array(static::BELONGS_TO, 'ElementRecord', 'required' => true, 'onDelete' => static::CASCADE)
        );
    }
}