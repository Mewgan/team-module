<?php

namespace Jet\Modules\Team\Controllers;

use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Media;
use Jet\Models\Website;
use Jet\Modules\Team\Models\Team;
use Jet\Modules\Team\Models\TeamRole;
use Jet\Modules\Team\Requests\TeamRequest;
use Jet\Services\Auth;

/**
 * Class AdminTeamController
 * @package Jet\Modules\Team\Controllers
 */
class AdminTeamController extends AdminController
{

    /**
     * @param $website
     * @return array
     */
    public function all($website)
    {
        if ($this->getWebsite($website)) {
            $params = [
                'websites' => $this->websites,
                'options' => $this->getWebsiteData($this->website)
            ];

            return ['status' => 'success', 'resource' => Team::repo()->listAll($params)];
        }
        return ['status' => 'error', 'Site non trouvé'];
    }


    /**
     * @param TeamRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function updateOrCreate(TeamRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'PUT') {
            if ($request->has('team')) {

                $team = $request->get('team');

                if (!$this->isWebsiteOwner($auth, $website))
                    return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour modifier ces contenus'];

                /** @var Website $website */
                $website = Website::findOneById($website);
                if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site'];

                foreach ($team as $value) {

                    $member = (isset($value['id']) && !empty($value['id']))
                        ? Team::findOneById($value['id'])
                        : new Team();

                    if(is_null($member)) return ['status' => 'error', 'message' => 'Impossible de trouver l\'équipe'];

                    /** @var Website $member_website */
                    $member_website = $member->getWebsite();

                    if ($member_website != $website && !is_null($member_website)) {
                        $data = $this->excludeData($website->getData(), 'teams', $member->getId());
                        $website->setData($data);
                        Website::watch($website);
                        $member = new Team();
                    }
                    $response = $this->updateFields($request, $member, $website, $value);
                    if (is_array($response)) return $response;

                    Team::watch($member);
                }

                if (Team::save()) {
                    $response = $this->all($website->getId());
                    return ($response['status'] == 'success')
                        ? ['status' => 'success', 'message' => 'L\'équipe a bien été mis à jour', 'resource' => $response['resource']]
                        : $response;
                }
                return ['status' => 'error', 'message' => 'L\'équipe n\'a pas pu être mis à jour'];
            }
            return ['status' => 'error', 'message' => 'Aucune donnée envoyée'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }


    /**
     * @param TeamRequest $request
     * @param Team $member
     * @param Website $website
     * @param $value
     * @return array|bool
     */
    private function updateFields(TeamRequest $request, Team $member, Website $website, $value)
    {
        $response = $request->validate($request->rules(), $request::$messages, $value);
        if ($response === true) {

            $member->setFullName($value['full_name']);
            $member->setDescription($value['description']);
            $member->setGender($value['gender']);
            $member->setPosition($value['position']);
            $member->setWebsite($website);

            if(isset($value['photo']) && !empty($value['photo'])){
                $photo = (isset($value['photo']['id']) && !empty($value['photo']['id']))
                    ? Media::findOneById($value['photo']['id'])
                    : Media:: findOneByPath($value['photo']['path']);
                if(is_null($photo)) return ['status' => 'error', 'message' => 'Impossible de trouver la photo'];
                $member->setPhoto($photo);
            }

            if(isset($value['roles']) && !empty($value['roles'])) {
                $roles = TeamRole::findBy(['id' => $value['roles']]);
                if (!is_null($roles)) $member->setRoles($roles);
            }else{
                $member->setRoles([]);
            }

            return true;
        }
        return $response;
    }


    /**
     * @param TeamRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(TeamRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {
            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
            $data = $website->getData();

            if (!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour supprimer ces collaborateurs'];

            $team = Team::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($team as $member) {
                if ($member['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'teams', $member['id']);
                } else
                    $ids[] = $member['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (Team::destroy($ids))
                ? ['status' => 'success', 'message' => 'Le collaborateur a bien été supprimé']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les collaborateur n\'a pas pu être supprimé'];
    }

}