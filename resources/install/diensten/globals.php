<?php
/**
 * Globals
 *
 * This'll create global groups and fields.
 *
 * The array keys are used to define the Global set's name.
 *
 * These fields will also be used to create the layout for the
 * global set, so you can add `required` to each field aswell.
 *
 * If you add an `for` key to the Field's array, that field will
 * be used to parse {variableName} variables.
 */
return array(
    'Aantal weergaven' => array(
        array(
            'for'          => 'overviewSection',
            'name'         => '{sectionName}',
            'handle'       => 'aantal{fieldName}',
            'type'         => 'Number',
            'translatable' => false,
            'value'        => '5',
            'settings'     => array(
                'min' => '0'
            )
        ),
        array(
            'for'          => 'overviewSection',
            'name'         => '{sectionName} widget',
            'handle'       => 'aantal{fieldName}',
            'type'         => 'Number',
            'translatable' => false,
            'value'        => '1',
            'settings'     => array(
                'min' => '0'
            )
        )
    ),
    'Entry ID\'s' => array(
        array(
            'for'          => 'overviewSection',
            'name'         => '{sectionName} entry ID',
            'handle'       => '{fieldName}',
            'value'        => '{sectionId}',
            'type'         => 'Number',
            'translatable' => false,
            'settings'     => array(
                'min' => '0'
            )
        )
    )
);