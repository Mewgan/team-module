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
            ->leftJoin('r.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        if(isset($params['member_in_role']) && $params['member_in_role'] === true){
            $query->addSelect('t')
                ->leftJoin('r.teams', 't');

            $query = $this->excludeData($query, $params['options'], 'teams');
        }

        $query->orderBy('r.id', 'DESC');

        return $query->getQuery()->getArrayResult();
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

        if (isset($params['options'])){
            $query = $this->excludeData($query, $params['options'], 'team_roles', 'r');
        }

        return $query;
    }

} 