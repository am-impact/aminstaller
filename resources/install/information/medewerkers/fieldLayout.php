<?php
/**
 * Field Layout
 *
 * This'll create the Field Layout for a section.
 * You get to point out which section will get any tabs.
 * After that you can add fields that should be shown there.
 *
 * For each field you can set some information:
 * - name:        The handle of a field.
 * - required:    Whether the field must contain data when creating / editing entries.
 * - testContent: [Optional] If you have set the option of test entries to true, you'll be able to set the test content for this field.
 *                           You can create an array of test content. The installer will randomly select one of the available options.
 *
 * In case of a Matrix field:
 * - type:   The Matrix Block handle.
 * - fields: An array with field handles with test content for that field.
 */
return array(
    'entrySection' => array(
        'Content' => array(
            array(
                'name'        => 'matrixTest',
                'required'    => false,
                'testContent' => array(
                    array(
                        'type' => 'blok1',
                        'fields' => array(
                            'tekst' => 'Tekst 1',
                            'nogEenTekst' => array('Nog een tekst 1', 'Nog een tekst 11', 'Nog een tekst 111', 'Nog een tekst 1111')
                        )
                    ),
                    array(
                        'type' => 'blok1',
                        'fields' => array(
                            'tekst' => 'Tekst 2',
                            'nogEenTekst' => 'Nog een tekst 2'
                        )
                    )
                )
            ),
            array(
                'name'        => 'afbeelding',
                'required'    => false,
                'testContent' => ''
            ),
            array(
                'name'        => 'functie',
                'required'    => false,
                'testContent' => 'Functienaam'
            ),
            array(
                'name'        => 'inDienstSinds',
                'required'    => false,
                'testContent' => '01-12-1988'
            ),
            array(
                'name'        => 'telefoonnummer',
                'required'    => false,
                'testContent' => '0123-456789'
            ),
            array(
                'name'        => 'eMailadres',
                'required'    => false,
                'testContent' => 'info@domein.nl'
            ),
            array(
                'name'        => 'website',
                'required'    => false,
                'testContent' => 'http://www.domein.nl'
            ),
            array(
                'name'        => 'omschrijving',
                'required'    => false,
                'testContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam adipiscing nec lectus quis mollis. Mauris vehicula nulla laoreet felis adipiscing porttitor sit amet a quam. Pellentesque fermentum at odio at ultrices. Nam purus tellus, porta quis nulla id, hendrerit iaculis augue. Cras porttitor enim pretium imperdiet placerat. Vestibulum urna turpis, rhoncus a magna eget, condimentum adipiscing felis. Morbi non erat non lacus scelerisque sollicitudin. Aenean pulvinar leo et eros faucibus, quis venenatis libero mattis. Duis tempor purus leo, non pretium risus suscipit non. Praesent feugiat eget nisl fermentum vehicula. Duis mollis posuere diam, vehicula tincidunt tellus eleifend vel.'
            )
        ),
        'Social media' => array(
            array(
                'name'        => 'facebookUrl',
                'required'    => false
            ),
            array(
                'name'        => 'googlePlusUrl',
                'required'    => false
            ),
            array(
                'name'        => 'linkedinUrl',
                'required'    => false
            ),
            array(
                'name'        => 'pinterestUrl',
                'required'    => false
            ),
            array(
                'name'        => 'twitterUrl',
                'required'    => false
            ),
            array(
                'name'        => 'youtubeUrl',
                'required'    => false
            )
        )
    )
);