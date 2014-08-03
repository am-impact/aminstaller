<?php
/**
 * Field Layout
 *
 * This file is optional for installing sections.
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
                'name'        => 'afbeelding',
                'required'    => false,
                'testContent' => ''
            ),
            array(
                'name'        => 'introTekst',
                'required'    => false,
                'testContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In semper interdum rhoncus.'
            ),
            array(
                'name'        => 'omschrijving',
                'required'    => true,
                'testContent' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In semper interdum rhoncus. Etiam et nulla sagittis, luctus mi id, dignissim urna. Quisque suscipit enim vitae lorem tincidunt, a imperdiet tellus pretium. Integer tellus mi, pulvinar ut molestie ac, faucibus ac tellus. Nullam ullamcorper nisi in diam fringilla, nec venenatis mauris varius. Duis accumsan neque commodo, ullamcorper erat vitae, consequat diam. Nullam tellus orci, bibendum quis eleifend sit amet, consequat eu tortor. Aliquam tincidunt interdum magna, vitae sollicitudin enim sodales ac. Ut consequat aliquet felis at placerat. Sed cursus magna dolor, a posuere purus vehicula quis. Aenean egestas laoreet leo vel malesuada. Etiam sit amet fermentum nibh, ut adipiscing nisi.'
            ),
            array(
                'name'        => 'geschrevenDoor',
                'required'    => false
            )
        ),
        'SEO' => array(
            array(
                'name'        => 'seoTitle',
                'required'    => false
            ),
            array(
                'name'        => 'seoDescription',
                'required'    => false
            ),
            array(
                'name'        => 'seoKeywords',
                'required'    => false
            )
        )
    )
);