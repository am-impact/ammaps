<?php
namespace Craft;

class m150513_122400_AmMaps_addLocaleSupport extends BaseMigration
{
    public function safeUp()
    {
        // Add column
        $this->addColumnAfter('ammaps_geomapper', 'locale', array(ColumnType::Locale, 'default' => null), 'lng');

        // Add index
        $this->createIndex('ammaps_geomapper', 'elementId,handle,locale');

        // Update existing records with the primary locale
        craft()->db->createCommand()->update('ammaps_geomapper', array('locale' => craft()->i18n->getPrimarySiteLocaleId()));
    }
}