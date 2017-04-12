<?php

namespace Jet\Modules\Team\Models;

use Doctrine\ORM\QueryBuilder;
use Jet\Models\AppRepository;

/**
 * Class TeamRoleRepository
 * @package Jet\Modules\Post\Models
 */
class TeamRoleRepository extends AppRepository
{

    /**
     * @param array $params
     * @return array
     */
    public function listAll($params = [])
    {
        $query = TeamRole::queryBuilder();

        $query->select('partial r.{id, slug, name}')
            ->from('Jet\Modules\Team\Models\TeamRole', 'r')
            ->leftJoin('r.website', 'w')
            ->addOrderBy('r.id', 'ASC');

        $query = $this->getQueryWithParams($query, $params);

        return $this->reassignTeam($query->getQuery()->getArrayResult(), $params);
    }

    /**
     * @param array $data
     * @param array $params
     * @return array
     */
    private function reassignTeam($data = [], $params = [])
    {
        if (isset($params['websites']) && isset($params['member_in_role']) && $params['member_in_role'] === true) {
            $roles = $data;
            $params['exclude_roles'] = isset($params['exclude_roles']) ? array_flip($params['exclude_roles']) : [];
            $replace_ids = isset($params['options']['parent_replace']['team_roles']) ? array_flip($params['options']['parent_replace']['team_roles']) : [];
            $exclude_ids = isset($params['options']['parent_exclude']['teams']) ? array_flip($params['options']['parent_exclude']['teams']) : [];

            $in_cat = null;
            if(isset($params['roles']) && is_array($params['roles']) && !empty($params['roles'])) {
                foreach ($params['roles'] as $k => $role) {
                    if (isset($params['exclude_roles'][$role])) {
                        unset($params['roles'][$k]);
                    }
                    if (isset($params['options']['parent_replace']['team_roles'][$role])) {
                        $params['roles'][$k] = $params['options']['parent_replace']['team_roles'][$role];
                    }
                }
                $in_cat = array_flip($params['roles']);
            }

            foreach ($data as $i => $role) {
                if (isset($params['exclude_roles'][$role['id']])) {
                    unset($data[$i]);
                }
                if (isset($replace_ids[$role['id']])) {
                    $data[$i] = $role;
                    if(count($role['teams']) <= 0){
                        $index = findIndex($roles, 'id', $replace_ids[$role['id']]);
                        if ($index !== false) {
                            $data[$i]['teams'] = $roles[$index]['teams'];
                        }
                    }
                }
                if(!is_null($in_cat) && isset($data[$i]['id']) && !isset($in_cat[$data[$i]['id']])){
                    unset($data[$i]);
                }
                foreach ($role['teams'] as $y => $team){
                    if(isset($exclude_ids[$team['id']])) unset($data[$i]['teams'][$y]);
                }
            }
        }
        return $data;
    }


    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = TeamRole::queryBuilder()
            ->select('partial r.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Team\Models\TeamRole', 'r')
            ->leftJoin('r.website', 'w');
        return $query->where($query->expr()->in('r.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, &$params)
    {

        if (isset($params['websites'])) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->in('w.id', ':websites'),
                    $query->expr()->isNull('w.id')
                )
            )->setParameter('websites', $params['websites']);
        } else {
            $query->andWhere($query->expr()->isNull('w.id'));
        }

        if(isset($params['member_in_role']) && $params['member_in_role'] === true){
            $query->addSelect('t')
                ->leftJoin('r.teams', 't')
                ->addOrderBy('t.position', 'ASC');

            if (isset($params['websites'])) {
                $query->leftJoin('t.website', 'tw')
                    ->andWhere($query->expr()->in('tw.id', ':websites'));
            }


            if (isset($params['options']['parent_exclude']['team_roles'])) {
                $params['exclude_roles'] = $params['options']['parent_exclude']['team_roles'];
                unset($params['options']['parent_exclude']['team_roles']);
            }
        }

        if (isset($params['options'])){
            $query = $this->excludeData($query, $params['options'], 'team_roles', 'r');
        }

        return $query;
    }

} 