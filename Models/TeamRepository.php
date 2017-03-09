<?php

namespace Jet\Modules\Team\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class TeamRepository
 * @package Jet\Modules\Team\Models
 */
class TeamRepository extends EntityRepository
{

    /**
     * @param array $params
     * @return array
     */
    public function listAll($params = [])
    {
        $query = TeamRole::queryBuilder();

        $query->select('t')
            ->addSelect('partial r.{id,name}')
            ->addSelect('partial p.{id,title,path,alt}')
            ->from('Jet\Modules\Team\Models\Team', 't')
            ->leftJoin('t.roles', 'r')
            ->leftJoin('t.photo', 'p')
            ->leftJoin('t.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        $query->orderBy('t.position', 'ASC');

        return $query->getQuery()->getArrayResult();
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

        if (isset($params['options']) && isset($params['options']['parent_exclude']) && isset($params['options']['parent_exclude']['team_roles']) && !empty($params['options']['parent_exclude']['team_roles'])) {
            $query->andWhere($query->expr()->notIn('t.id', ':exclude_ids'))
                ->setParameter('exclude_ids', $params['options']['parent_exclude']['team_roles']);
        }

        return $query;
    }
} 