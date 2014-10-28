<?php
/**
 * Sections
 *
 * Important: If you wish to install sections, you need the following file!
 * - required `templateGroup.php`
 * - optional `fieldLayout.php`
 * - optional `entries.php`
 *
 * This'll create sections.
 *
 * The array key here will be used in the optional files aswell!
 *
 * Available types: Single, Channel or Structure.
 */
return array(
    'overviewSection' => array(
        'type'      => 'Single',
        'label'     => 'Overzicht',
        'name'      => 'Diensten',
        'urlFormat' => 'diensten',
        'info'      => 'Naam van het overzicht in de CP.'
    ),
    'entrySection' => array(
        'type'      => 'Structure',
        'label'     => 'Entry',
        'name'      => 'Dienst',
        'urlFormat' => 'diensten/{slug}',
        'info'      => 'Naam van de entry in de CP.'
    )
);