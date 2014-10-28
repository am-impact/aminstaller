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
 * If you add an `instructions` key to the Field's array, that field will
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
            'name'         => 'Gerelateerde diensten',
            'handle'       => 'gerelateerdeDiensten',
            'type'         => 'Entries',
            'translatable' => false,
            'settings' => array(
                'section' => 'Dienst' // It will only allow entries from this section to be selected, if available. Otherwise, all sections
            )
        )
    ),
    'Medewerker' => array(
        array(
            'name'     => 'Functie',
            'handle'   => 'functie',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'In dienst sinds',
            'handle'   => 'inDienstSinds',
            'type'     => 'PlainText'
        )
    ),
    'NAW gegevens' => array(
        array(
            'name'     => 'E-mailadres',
            'handle'   => 'eMailadres',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'Telefoonnummer',
            'handle'   => 'telefoonnummer',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'Website',
            'handle'   => 'website',
            'type'     => 'PlainText'
        )
    ),
    'Social media' => array(
        array(
            'name'     => 'Facebook URL',
            'handle'   => 'facebookUrl',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'Google plus URL',
            'handle'   => 'googlePlusUrl',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'LinkedIn URL',
            'handle'   => 'linkedinUrl',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'Pinterest URL',
            'handle'   => 'pinterestUrl',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'Twitter URL',
            'handle'   => 'twitterUrl',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'Youtube URL',
            'handle'   => 'youtubeUrl',
            'type'     => 'PlainText'
        )
    )
);