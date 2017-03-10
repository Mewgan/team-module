<?php

namespace Jet\Modules\Team\Controllers;

use Jet\FrontBlock\Controllers\MainController;
use Jet\Models\Content;
use Jet\Models\Website;
use Jet\Modules\Team\Models\Team;

class FrontTeamController extends MainController
{

    /**
     * @param Website $website
     * @param Content $content
     * @return null
     */
    public function show(Website $website, $content){
        $data = $content->getData();
        if (!empty($data)) {
            if (empty($this->websites)) {
                $this->websites[] = $website;
                $this->getThemeWebsites($website);
            }
            $params = [
                'websites' => $this->websites,
                'options' => $this->getWebsiteData($website),
                'roles' => isset($data['roles']) ? $data['roles'] : []
            ];

            $team = Team::repo()->listAll($params);
            return $this->_renderContent($content->getTemplate(), 'src/Modules/Team/Views/', compact('team'));
        }
        return null;
    }
    
}