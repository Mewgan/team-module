<?php

namespace Jet\Modules\Team\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class TeamRoleRepository
 * @package Jet\Modules\Post\Models
 */
class TeamRoleRepository extends EntityRepository
{


    /**
     * @param array $params
     * @return array
     */
    public function listAll($params = [])
    {
        $query = TeamRole::queryBuilder();

        $query->select('t')
            ->from('Jet\Modules\Team\Models\TeamRole', 't')
            ->leftJoin('t.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        $query->orderBy('t.id', 'DESC');

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = TeamRole::queryBuilder()
            ->select('partial t.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Team\Models\TeamRole', 't')
            ->leftJoin('t.website', 'w');
        return $query->where($query->expr()->in('t.id', ':ids'))
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

        if (isset($params['options']) && isset($params['options']['parent_exclude']) && isset($params['options']['parent_exclude']['team_roles']) && !empty($params['options']['parent_exclude']['team_roles'])) {
            $query->andWhere($query->expr()->notIn('t.id', ':exclude_ids'))
                ->setParameter('exclude_ids', $params['options']['parent_exclude']['team_roles']);
        }

        return $query;
    }

} 