<?php

namespace Jet\Modules\Team\Services;


use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Team\Models\Team;
use Jet\Modules\Team\Models\TeamRole;

trait LoadTeamFixture
{

    /**
     * @param ObjectManager $manager
     */
    public function loadTeam(ObjectManager $manager)
    {
        foreach ($this->data as $key => $data) {
            $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);
            $team = (Team::where('full_name', $data['full_name'])->where('website', $website)->count() == 0)
                ? new Team()
                : Team::findOneBy(['full_name' => $data['full_name'], 'website' => $website]);
            $team->setFullName($data['full_name']);
            $team->setPosition($data['position']);
            $team->setGender($data['gender']);
            $team->setDescription($data['description']);
            $team->setWebsite($website);
            if($this->hasReference($data['photo']))
                $team->setPhoto($this->getReference($data['photo']));
            if(isset($data['roles']) && is_array($data['roles'])){
                foreach ($data['roles'] as $role){
                    $role = ($this->hasReference($role))
                        ? $this->getReference($role)
                        : TeamRole::findOneBy(['name' => $role, 'website' => $website]);
                    $team->addRole($role);
                }
            }
            $this->setReference($key, $team);
            $manager->persist($team);
        }

        $manager->flush();
    }

}