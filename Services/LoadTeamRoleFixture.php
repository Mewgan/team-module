<?php

namespace Jet\Modules\Team\Services;


use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Team\Models\Team;
use Jet\Modules\Team\Models\TeamRole;

trait LoadTeamRoleFixture
{

    /**
     * @param ObjectManager $manager
     */
    public function loadTeamRole(ObjectManager $manager)
    {
        foreach ($this->data as $key => $data) {
            $website = null;
            if(isset($data['website']) && !is_null($data['website']))
                $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);

            $team_role = (TeamRole::where('name', $data['name'])->where('website', $website)->count() == 0)
                ? new TeamRole()
                : TeamRole::findOneBy(['name' => $data['name'], 'website' => $website]);
            $team_role->setName($data['name']);
            $team_role->setSlug($data['slug']);
            if(!is_null($website))
                $team_role->setWebsite($website);
            $this->setReference($key, $team_role);
            $manager->persist($team_role);
        }

        $manager->flush();
    }

    /**
     * @param $data
     * @param Website $website
     * @return mixed
     */
    public function getTeamRolesContent($data, Website $website)
    {
        if (isset($data['data']) && isset($data['data']['roles']) && is_array($data['data']['roles'])) {
            $new_roles = [];
            foreach ($data['data']['roles'] as $role){
                $role = ($this->hasReference($role))
                    ? $this->getReference($role) : TeamRole::findOneBy(['name' => $role, 'website' => $website]);
                $new_roles[] = $role->getId();
            }
            $data['data']['roles'] = $new_roles;
        }
        return $data;
    }

}