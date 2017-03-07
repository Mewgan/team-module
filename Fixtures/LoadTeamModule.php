<?php
namespace Jet\Modules\Team\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Services\LoadFixture;

class LoadTeamModule extends AbstractFixture implements DependentFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'module_price' => [
            'name' => 'Équipe',
            'slug' => 'team',
            'callback' => 'Jet\Modules\Team\Controllers\FrontTeamController@show',
            'description' => 'Affiche les équipes',
            'category' => 'team',
            'access_level' => 4
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModule($manager);
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            'Jet\Modules\Team\Fixtures\LoadTeamModuleCategory'
        ];
    }
}