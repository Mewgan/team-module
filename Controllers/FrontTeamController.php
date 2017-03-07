<?php

namespace Jet\Modules\Team\Controllers;

use Jet\FrontBlock\Controllers\MainController;

class FrontTeamController extends MainController
{

    /**
     * @param $content
     * @return null
     */
    public function show($content){
        $data = $content->getData();
        if(!empty($data)) {
            return (empty($data['content']))
                ? null
                : $data['content'];
        }
        return null;
    }
    
}