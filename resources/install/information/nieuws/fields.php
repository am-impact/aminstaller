<?php
/**
 * Fields
 *
 * This'll create fields.
 *
 * The array keys are used to define the Field Group's name.
 *
 * You have to specify the handle yourself, because there might come
 * a moment where a field has the same name, but a different handle.
 * Since the handle is the only thing that is unique about a field,
 * you have to set the name yourself. Make sure it's a camelCasedName!
 */
return array(
    'Standaard' => array(
        array(
            'name'     => 'Afbeelding',
            'handle'   => 'afbeelding',
            'type'     => 'Assets',
            'settings' => array(
                'restrictFiles' => 1,
                'allowedKinds'  => array('image'),
                'limit'         => 1
            )
        ),
        array(
            'name'     => 'Intro tekst',
            'handle'   => 'introTekst',
            'type'     => 'RichText',
            'settings' => array(
                'configFile' => 'Tekst met opmaak.json'
            )
        ),
        array(
            'name'     => 'Omschrijving',
            'handle'   => 'omschrijving',
            'type'     => 'RichText',
            'settings' => array(
                'configFile' => 'Alles.json'
            )
        )
    ),
    'Koppelingen' => array(
        array(
            'name'     => 'Geschreven door',
            'handle'   => 'geschrevenDoor',
            'type'     => 'Entries',
            'settings' => array(
                'section' => 'Medewerker', // It will only allow entries from this section to be selected, if available. Otherwise, all sections
                'limit'   => 1
            )
        )
    )
);