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
        'name'      => 'Nieuws',
        'urlFormat' => 'nieuws',
        'info'      => 'Naam van het overzicht in de CP.'
    ),
    'entrySection' => array(
        'type'      => 'Channel',
        'label'     => 'Entry',
        'name'      => 'Nieuwsbericht',
        'urlFormat' => 'nieuws/{slug}',
        'info'      => 'Naam van de entry in de CP.'
    )
);