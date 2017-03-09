<?php

return [
    '/module/team/*' => [
        'use' => 'AdminTeamController@{method}',
        'ajax' => true
    ],

    '/module/team-role/*' => [
        'use' => 'AdminTeamRoleController@{method}',
        'ajax' => true
    ],
];