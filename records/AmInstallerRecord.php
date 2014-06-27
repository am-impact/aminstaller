<?php
namespace Craft;

class AmInstallerRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'aminstaller';
    }

    protected function defineAttributes()
    {
        return array(
            'handle'    => AttributeType::String,
            'installed' => AttributeType::Bool
        );
    }
}