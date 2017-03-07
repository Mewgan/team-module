<?php

namespace Jet\Modules\Team\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class TeamRepository
 * @package Jet\Modules\Team\Models
 */
class TeamRepository extends EntityRepository
{

    /**
     * @param $page
     * @param $max
     * @param array $params
     * @return array
     */
    public function listAll($page, $max, $params = [])
    {

        $countSearch = false;
        /** @var QueryBuilder $query */
        $query = Team::queryBuilder();

        $query->select('p')
            ->addSelect('partial c.{id,name,slug}')
            ->addSelect('partial t.{id,path,alt}')
            ->addSelect('partial w.{id,domain}')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w')
            ->leftJoin('p.thumbnail', 't')
            ->leftJoin('p.categories', 'c');

        if (isset($params['total_row']) && !empty($params['total_row'])) {
            $countSearch = true;
            $query->setMaxResults($params['total_row']);
        } else {
            $query->setFirstResult(($page - 1) * $max)
                ->setMaxResults($max);
        }

        $query = $this->getQueryWithParams($query, $params);

        if (isset($params['search']) && !empty($params['search'])) {
            $countSearch = true;
            $query->andWhere($query->expr()->orX(
                $query->expr()->like('p.title', ':search'),
                $query->expr()->like('p.description', ':search'),
                $query->expr()->like('c.name', ':search')
            ))->setParameter('search', '%' . $params['search'] . '%');
        }

        if (isset($params['filter']) && !empty($params['filter']) && !empty($params['filter']['column'])) {
            $countSearch = true;
            $op = (isset($params['filter']['operator'])) ? $params['filter']['operator'] : 'eq';
            if($op == 'isNull')
                $query->andWhere($query->expr()->isNull($params['filter']['column']));
            elseif ($op == 'isNotNull')
                $query->andWhere($query->expr()->isNotNull($params['filter']['column']));
            else
                $query->andWhere($query->expr()->$op($params['filter']['column'], ':value'))
                    ->setParameter('value', $params['filter']['value']);
        }

        (isset($params['order']) && !empty($params['order']) && !empty($params['order']['column']))
            ? $query->addOrderBy($params['order']['column'], strtoupper($params['order']['dir']))
            : $query->orderBy('p.id', 'DESC');

        $pg = new Paginator($query);
        $data = $pg->getQuery()->getArrayResult();
        return ['data' => $data, 'total' => ($countSearch) ? count($data) : (int)$this->countPost($params)];
    }

    /**
     * @param array $params
     * @return int
     */
    public function countPost($params = [])
    {
        $query = Post::queryBuilder();

        $query->select('COUNT(p)')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, $params)
    {
        if (isset($params['published'])) {
            $query->where($query->expr()->eq('p.published', ':published'))
                ->setParameter('published', $params['published']);
        }

        if (isset($params['websites']) && !empty($params['websites'])) {
            $query->andWhere($query->expr()->in('w.id', ':websites'))
                ->setParameter('websites', $params['websites']);
        }

        if (isset($params['options']) && isset($params['options']['parent_exclude'])) {
            if (isset($params['options']['parent_exclude']['posts']) && !empty($params['options']['parent_exclude']['posts'])) {
                $query->andWhere($query->expr()->notIn('p.id', ':exclude_post_ids'))
                    ->setParameter('exclude_post_ids', $params['options']['parent_exclude']['posts']);
            }
/*
            if(isset($params['options']['parent_exclude']['post_categories']) && !empty($params['options']['parent_exclude']['post_categories'])){
                $query->andWhere($query->expr()->notIn('c.id',':exclude_category_ids'))
                    ->setParameter('exclude_category_ids',$params['options']['parent_exclude']['post_categories']);
            }*/
        }

        if (isset($params['db']) && !empty($params['db'])) {
            foreach ($params['db'] as $key => $db) {
                if (isset($db['type'])) {
                    if ($db['type'] == 'dynamic' && isset($db['route']) && !empty($db['route']) && isset($params['params'][$db['route']])) {
                        $query->andWhere($query->expr()->eq($db['alias'] . '.' . $db['column'], ':column_' . $key))
                            ->setParameter('column_' . $key, $params['params'][$db['route']]);
                    } elseif ($db['type'] == 'static' && isset($db['value']) && !empty($db['value'])) {
                        $replace_content = ($db['alias'] == 'p') ? 'posts' : 'post_categories';
                        if (is_array($db['value'])) {
                            if (isset($params['options']['parent_replace']) && isset($params['options']['parent_replace'][$replace_content])) {
                                foreach ($db['value'] as $k => $id) {
                                    if (isset($params['options']['parent_replace'][$replace_content][$id]))
                                        $db['value'][$k] = $params['options']['parent_replace'][$replace_content][$id];
                                }
                            }
                            $query->andWhere($query->expr()->in($db['alias'] . '.id', ':column_' . $key))
                                ->setParameter('column_' . $key, $db['value']);
                        } else {
                            if (isset($params['options']['parent_replace']) && isset($params['options']['parent_replace'][$replace_content]) && isset($params['options']['parent_replace'][$replace_content][$db['value']]))
                                $db['value'] = $params['options']['parent_replace'][$replace_content][$db['value']];
                            $query->andWhere($query->expr()->eq($db['alias'] . '.id', ':column_' . $key))
                                ->setParameter('column_' . $key, $db['value']);
                        }
                    }
                }
            }
        }

        return $query;
    }

    /**
     * @param $id
     * @param $keys
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function retrieveData($id, $keys){
        $query = Team::queryBuilder()
            ->select($keys)
            ->from('Jet\Modules\Team\Models\Team','t')
            ->where('t.id = :id')
            ->setParameter('id', $id);
        return $query->getQuery()->getOneOrNullResult();
    }

} 