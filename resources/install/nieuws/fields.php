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
 *
 * If you add an instruction key to the Field's array, that field will
 * show instructions that users will see when creating / editing entries.
 */
return array(
    'Standaard' => array(
        array(
            'name'         => 'Afbeelding',
            'handle'       => 'afbeelding',
            'type'         => 'Assets',
            'translatable' => false,
            'settings'     => array(
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
            'name'         => 'Geschreven door',
            'handle'       => 'geschrevenDoor',
            'type'         => 'Entries',
            'translatable' => false,
            'settings' => array(
                'section' => 'Medewerker', // It will only allow entries from this section to be selected, if available. Otherwise, all sections
                'limit'   => 1
            )
        )
    ),
    'SEO' => array(
        array(
            'name'         => 'Title',
            'handle'       => 'seoTitle',
            'type'         => 'PlainText',
            'instructions' => ''
        ),
        array(
            'name'         => 'Description',
            'handle'       => 'seoDescription',
            'type'         => 'PlainText',
            'instructions' => ''
        ),
        array(
            'name'         => 'Keywords',
            'handle'       => 'seoKeywords',
            'type'         => 'PlainText',
            'instructions' => ''
        )
    )
);