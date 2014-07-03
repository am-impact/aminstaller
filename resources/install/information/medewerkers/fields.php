<?php
return array(
    'Standaard' => array(
        array(
            'name'     => 'Matrix test',
            'handle'   => 'matrixTest',
            'type'     => 'Matrix',
            'settings' => array(
                'maxBlocks' => 4,
                'blockTypes' => array(
                    'new1' => array(
                        'name' => 'Blok 1',
                        'handle' => 'blok1',
                        'fields' => array(
                            'new1' => array(
                                'name' => 'Tekst',
                                'handle' => 'tekst',
                                'required' => false,
                                'type' => 'PlainText',
                                'typesettings' => array()
                            ),
                            'new2' => array(
                                'name' => 'Nog een tekst',
                                'handle' => 'nogEenTekst',
                                'required' => false,
                                'type' => 'PlainText',
                                'typesettings' => array()
                            )
                        )
                    )
                )
            )
        ),
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
            'name'     => 'Omschrijving',
            'handle'   => 'omschrijving',
            'type'     => 'RichText',
            'settings' => array(
                'configFile' => 'Alles.json'
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