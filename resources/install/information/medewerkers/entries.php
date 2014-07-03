<?php
return array(
    'entrySection' => array(
        array(
            'type'    => 'checkbox',
            'name'    => 'installTestEntries',
            'value'   => 1,
            'checked' => true,
            'label'   => 'Installeer test entries?'
        ),
        array(
            'type'    => 'select',
            'name'    => 'totalTestEntries',
            'value'   => 1,
            'options' => array_combine(range(1,10), range(1,10)),
            'label'   => 'Het aantal te installeren entries'
        )
    )
);