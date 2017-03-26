<?php

namespace Jet\Modules\Team\Models;

use Doctrine\ORM\QueryBuilder;
use Jet\Models\AppRepository;

/**
 * Class TeamRepository
 * @package Jet\Modules\Team\Models
 */
class TeamRepository extends AppRepository
{

    /**
     * @param array $params
     * @return array
     */
    public function listAll($params = [])
    {
        $query = Team::queryBuilder();

        $query->select('t')
            ->addSelect('partial r.{id,name}')
            ->addSelect('partial p.{id,title,path,alt}')
            ->from('Jet\Modules\Team\Models\Team', 't')
            ->leftJoin('t.roles', 'r')
            ->leftJoin('t.photo', 'p')
            ->leftJoin('t.website', 'w')
            ->orderBy('t.position', 'ASC');

        $query = $this->getQueryWithParams($query, $params);

        return $this->reassignRoles($query->getQuery()->getArrayResult(), $params);
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = Team::queryBuilder()
            ->select('partial t.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Team\Models\Team', 't')
            ->leftJoin('t.website', 'w');
        return $query->where($query->expr()->in('t.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param array $data
     * @param array $params
     * @return array
     */
    private function reassignRoles($data = [], $params = [])
    {
        $roles = TeamRole::repo()->listAll(['websites' => $params['websites']]);
        $exclude_ids = isset($params['options']['parent_exclude']['team_roles']) ? array_flip($params['options']['parent_exclude']['team_roles']) : [];
        $in_cat = null;
        if (isset($params['roles']) && is_array($params['roles']) && !empty($params['roles'])) {
            foreach ($params['roles'] as $k => $role) {
                if (isset($exclude_ids[$role])) {
                    unset($params['roles'][$k]);
                }
                if (isset($params['options']['parent_replace']['team_roles'][$role])) {
                    $params['roles'][$k] = $params['options']['parent_replace']['team_roles'][$role];
                }
            }
            $in_cat = array_flip($params['roles']);
        }
        foreach ($data as $i => $member) {
            $remove_item = true;
            if (isset($member['roles']) && is_array($member['roles']) && !empty($member['roles'])) {
                foreach ($member['roles'] as $y => $role) {
                    if (isset($exclude_ids[$role['id']])) {
                        unset($data[$i]['roles'][$y]);
                    }
                    if (isset($params['options']['parent_replace']['team_roles'][$role['id']])) {
                        $index = findIndex($roles, 'id', $params['options']['parent_replace']['team_roles'][$role['id']]);
                        if ($index !== false) {
                            $data[$i]['roles'][$y] = $roles[$index];
                        }
                    }
                    if (is_null($in_cat) || (isset($data[$i]['roles'][$y]['id']) && isset($in_cat[$data[$i]['roles'][$y]['id']]))){
                        $remove_item = false;
                    }
                }
            }
            if($remove_item === true){
                unset($data[$i]);
            }
        }
        return $data;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return QueryBuilder
     */
    private function getQueryWithParams(QueryBuilder $query, $params)
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

        if (isset($params['options'])) {
            $query = $this->excludeData($query, $params['options'], 'teams');
        }

        return $query;
    }
}