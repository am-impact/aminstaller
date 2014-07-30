<?php
/**
 * Sections
 *
 * This'll create sections.
 *
 * The array key here will be used in other files aswell!
 * Available types: Single, Channel or Structure.
 */
return array(
    'overviewSection' => array(
        'type'      => 'Single',
        'name'      => 'Overzicht',
        'label'     => 'Medewerker overzicht',
        'urlFormat' => 'medewerkers',
        'info'      => 'Naam van het overzicht in de CP.'
    ),
    'entrySection' => array(
        'type'      => 'Channel',
        'name'      => 'Entry',
        'label'     => 'Medewerker',
        'urlFormat' => 'medewerkers/{slug}',
        'info'      => 'Naam van de entry in de CP.'
    )
);