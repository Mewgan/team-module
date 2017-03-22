<?php

namespace Jet\Modules\Team\Controllers;

use Jet\FrontBlock\Controllers\MainController;
use Jet\Models\Content;
use Jet\Models\Website;
use Jet\Modules\Team\Models\Team;
use Jet\Modules\Team\Models\TeamRole;

class FrontTeamController extends MainController
{

    /**
     * @param Website $website
     * @param Content $content
     * @return null
     */
    public function show(Website $website, $content)
    {
        $data = $content->getData();
        if (!empty($data)) {
            if (empty($this->websites)) {
                $this->websites[] = $website;
                $this->getThemeWebsites($website);
            }
            $params = [
                'websites' => $this->websites,
                'options' => $this->getWebsiteData($website),
                'roles' => isset($data['roles']) ? $data['roles'] : [],
                'member_in_role' => (isset($data['member_in_role']) && ((string)$data['member_in_role'] == 'true' || $data['member_in_role'] == true)) ? true : false
            ];

            $team = (isset($data['member']) && ((string)$data['member'] == 'false' || $data['member'] == false)) ? [] : Team::repo()->listAll($params);
            $roles = (isset($data['role']) && ((string)$data['role'] == 'false' || $data['role'] == false)) ? [] : TeamRole::repo()->listAll($params);
            return $this->_renderContent($content->getTemplate(), 'src/Modules/Team/Views/', compact('team', 'roles'));
        }
        return null;
    }

}