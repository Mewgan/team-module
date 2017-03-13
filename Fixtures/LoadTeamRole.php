<?php
namespace Jet\Modules\Team\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Modules\Team\Services\LoadTeamRoleFixture;

class LoadTeamRole extends AbstractFixture
{

    use LoadTeamRoleFixture;
    
    protected $data = [
        'owner-role' => [
            'name' => 'Propriétaire',
            'slug' => 'proprietaire',
        ],
        'barber-role' => [
            'name' => 'Coiffeur',
            'slug' => 'coiffeur',
        ],
    ];

    public function load(ObjectManager $manager)
    {
       $this->loadTeamRole($manager);
    }

}