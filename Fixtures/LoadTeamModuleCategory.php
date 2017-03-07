<?php
namespace Jet\Modules\Team\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;

class LoadTeamModuleCategory extends AbstractFixture
{
    use LoadFixture;

    protected $data = [
        'name' => 'Team',
        'title' => 'Équipe',
        'slug' => 'team',
        'nav' => true,
        'description' => 'Module pour gérer vos équipes',
        'icon' => 'fa fa-users',
        'author' => 'S.Sumugan',
        'version' => '0.1',
        'update_available' => false,
        'access_level' => 4
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModuleCategory($manager);
    }
}