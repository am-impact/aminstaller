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
    'Globaal SEO' => array(
        array(
            'name'         => 'Description',
            'handle'       => 'seoGlobalDescription',
            'type'         => 'PlainText',
            'instructions' => 'Description voor de gehele site indien dit niet op een andere pagina is gevuld.'
        ),
        array(
            'name'         => 'Keywords',
            'handle'       => 'seoGlobalKeywords',
            'type'         => 'PlainText',
            'instructions' => 'Keywords voor de gehele site indien dit niet op een andere pagina is gevuld.'
        )
    )
);