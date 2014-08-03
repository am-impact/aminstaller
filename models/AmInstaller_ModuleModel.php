<?php
namespace Craft;

class AmInstaller_ModuleModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'name'          => AttributeType::String,
            'handle'        => AttributeType::String,
            'description'   => AttributeType::String,
            'installed'     => AttributeType::Bool,
            'tabs'          => AttributeType::Enum,
            'main'          => AttributeType::Enum,
            'sections'      => AttributeType::Enum,
            'globals'       => AttributeType::Enum,
            'fields'        => AttributeType::Enum,
            'fieldLayout'   => AttributeType::Enum,
            'templateGroup' => AttributeType::Enum,
            'entries'       => AttributeType::Enum
        );
    }
}