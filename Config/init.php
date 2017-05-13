<?php

return [

    'app' => [
        'Team' => [
            'order' => 3,
            'hook' => [
                'left_sidebar' => true
            ]
        ],
        'blocks' => [
            'TeamModule' => [
                'path' => 'src/Modules/Team/',
                'namespace' => '\\Jet\\Modules\\Team',
                'view_dir' => 'src/Modules/Team/Views/',
                'prefix' => 'admin',
            ],
        ],
        'fixtures' => [
            'src/Modules/Team/Fixtures/'
        ]
    ]
];