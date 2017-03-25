<?php

namespace Jet\Modules\Team\Controllers;

use Cocur\Slugify\Slugify;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Website;
use Jet\Modules\Team\Models\Team;
use Jet\Modules\Team\Models\TeamRole;
use Jet\Services\Auth;
use JetFire\Http\Request;

/**
 * Class AdminTeamRoleController
 * @package Jet\Modules\Team\Controllers
 */
class AdminTeamRoleController extends AdminController
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

            return ['status' => 'success', 'resource' => TeamRole::repo()->listAll($params)];
        }
        return ['status' => 'error', 'Site non trouvé'];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $website
     * @return array
     */
    public function create(Request $request, Auth $auth, Slugify $slugify, $website)
    {
        if ($request->method() == 'POST') {

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour créer un rôle'];

            if(!$request->has('name'))
                return ['status' => 'error', 'message' => 'Aucune donnée reçue'];

            $name = $request->get('name');
            if (TeamRole::where('name', $name)->where('website', $website)->count() == 0) {
                $role = new TeamRole();
                $role->setName($name);
                $role->setSlug($slugify->slugify($name));
                $role->setWebsite(Website::findOneById($website));
                if (TeamRole::watchAndSave($role))
                    return ['status' => 'success', 'message' => 'Le rôle a bien été créé', 'resource' => $role];
                return ['status' => 'error', 'message' => 'Le rôle n\'a pas été créé'];
            }
            return ['status' => 'error', 'message' => 'Le rôle existe déjà'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $id
     * @param $website
     * @return array
     */
    public function update(Request $request, Auth $auth, Slugify $slugify, $id, $website)
    {
        if ($request->method() == 'PUT') {

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour mettre à jour un rôle'];

            $name = $request->get('name');
            $replace = false;

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

            if (TeamRole::where('id', '!=', $id)->where('name', $name)->where('website', $website)->count() > 0)
                return ['status' => 'error', 'message' => 'Le rôle existe déjà'];

            /** @var TeamRole $role */
            $role = TeamRole::findOneById($id);
            if (is_null($role)) return ['status' => 'error', 'message' => 'Impossible de trouver le rôle'];

            if (is_null($role->getWebsite()) || $role->getWebsite()->getId() != $website->getId()) {
                $data = $this->excludeData($website->getData(), 'team_roles', $role->getId());
                $website->setData($data);
                Website::watch($website);

                $role = new TeamRole();
                $replace = true;
            }

            $role->setName($name);
            $role->setSlug($slugify->slugify($name));
            $role->setWebsite($website);

            if (TeamRole::watchAndSave($role)) {
                if ($replace) {
                    $website = $role->getWebsite();
                    $data = $this->replaceData($website->getData(), 'team_roles', $id, $role->getId());
                    $website->setData($data);
                    Website::watchAndSave($website);
                }
                return ['status' => 'success', 'message' => 'Le rôle a bien été mis à jour'];
            } else
                return ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(Request $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

            if (!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour supprimer ces rôles'];

            $data = $website->getData();

            $roles = TeamRole::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($roles as $role) {
                $data = $this->removeData($data, 'team_roles', $role['id']);
                if ($role['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'team_roles', $role['id']);
                } else
                    $ids[] = $role['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (TeamRole::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les rôles ont bien été supprimés']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les rôles n\'ont pas pu être supprimés'];
    }

}