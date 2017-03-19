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
            ->leftJoin('t.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        if(isset($params['roles']) && !empty($params['roles'])){
            $query->andWhere($query->expr()->in('r.id', ':roles'))
                ->setParameter('roles', $params['roles']);
        }

        $query->orderBy('t.position', 'ASC');

        return $query->getQuery()->getArrayResult();
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
        
        if (isset($params['options'])){
            $query = $this->excludeData($query, $params['options'], 'teams');
        }

        return $query;
    }
} 