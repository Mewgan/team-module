<?php

namespace Jet\Modules\Team\Requests;

use JetFire\Framework\System\Request;

/**
 * Class TeamRequest
 * @package Jet\Modules\Team\Requests
 */
class TeamRequest extends Request
{

    /**
     * @var array
     */
    public static $messages = [
        'required' => 'Le champ ":field" doit être rempli',
    ];


    /**
     * @return array
     */
    public function rules()
    {
        return [
            'full_name|gender|position' => 'required',
            'roles|description|photo' => 'optional',
        ];
    }

}