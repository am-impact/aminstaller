<?php
return array(
    'FieldGroupName' => array(
        array(
            'name'     => 'Assets',
            'handle'   => '',
            'type'     => 'Assets',
            'settings' => array(
                'restrictFiles' => 1,
                'allowedKinds'  => array('image'),
                'limit'         => 1
            )
        ),
        array(
            'name'     => 'Assets',
            'handle'   => '',
            'type'     => 'Assets',
            'settings' => array(
                'allowedKinds'  => array('image')
            )
        ),
        array(
            'name'     => 'Assets',
            'handle'   => '',
            'type'     => 'Assets'
        ),
        array(
            'name'     => 'Entries',
            'handle'   => '',
            'type'     => 'Entries'
        ),
        array(
            'name'     => 'Entries',
            'handle'   => '',
            'type'     => 'Entries',
            'settings' => array(
                'limit' => 1
            )
        ),
        array(
            'name'     => 'Matrix',
            'handle'   => '',
            'type'     => 'Matrix',
            'settings' => array(
                'maxBlocks' => '',
                'blockTypes' => array(
                    'new1' => array(
                        'name' => 'Blok 1',
                        'handle' => 'blok1',
                        'fields' => array(
                            'new1' => array(
                                'name'         => 'PlainText',
                                'handle'       => '',
                                'required'     => false,
                                'type'         => 'PlainText',
                                'typesettings' => array()
                            ),
                            'new2' => array(
                                'name'         => 'PlainText',
                                'handle'       => '',
                                'required'     => false,
                                'type'         => 'PlainText',
                                'typesettings' => array()
                            )
                        )
                    )
                )
            )
        ),
        array(
            'name'     => 'Number',
            'handle'   => '',
            'type'     => 'Number',
            'settings' => array(
                'min' => '0'
            )
        ),
        array(
            'name'     => 'PlainText',
            'handle'   => '',
            'type'     => 'PlainText'
        ),
        array(
            'name'     => 'PlainText',
            'handle'   => '',
            'type'     => 'PlainText',
            'settings' => array(
                'multiline'   => 1,
                'initialRows' => 4
            )
        ),
        array(
            'name'     => 'RichText',
            'handle'   => '',
            'type'     => 'RichText',
            'settings' => array(
                'configFile' => 'Alles.json'
            )
        )
    )
);