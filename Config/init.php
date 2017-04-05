<?php

return [

    'app' => [
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