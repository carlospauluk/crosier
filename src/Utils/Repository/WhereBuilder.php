<?php
namespace App\Utils\Repository;

use Doctrine\ORM\QueryBuilder;

class WhereBuilder
{

    public $filterTypes = array(
        'EQ' => 1,
        'NEQ' => 1,
        'LT' => 1,
        'LTE' => 1,
        'GT' => 1,
        'GTE' => 1,
        'IS_NULL' => 0,
        'IS_NOT_NULL' => 0,
        'IN' => 999,
        'NOT_IN' => 999,
        'LIKE' => 1,
        'NOT_LIKE' => 1,
        'BETWEEN' => 2
    );

    /**
     *
     * @param QueryBuilder $qb
     * @param array $filters
     * @return \Doctrine\ORM\Query\Expr\Comparison[]|string[]|NULL[]
     */
    public static function build(QueryBuilder &$qb, $filters)
    {
        $andX = $qb->expr()->andX();
        
        foreach ($filters as $filter) {
            
            $field_array = is_array($filter->field) ? $filter->field : array(
                $filter->field
            );
            
            $orX = $qb->expr()->orX();
            
            foreach ($field_array as $field) {
                
                
                switch ($filter->compar) {
                    case 'EQ':
                        $orX->add($qb->expr()
                            ->eq('e.' . $field, ':' . $field));
                        break;
                    case 'NEQ':
                        $orX->add($qb->expr()
                            ->neq('e.' . $field, ':' . $field));
                        break;
                    case 'LT':
                        $orX->add($qb->expr()
                            ->lt('e.' . $field, ':' . $field));
                        break;
                    case 'LTE':
                        $orX->add($qb->expr()
                            ->lte('e.' . $field, ':' . $field));
                        break;
                    case 'GT':
                        $orX->add($qb->expr()
                            ->gt('e.' . $field, ':' . $field));
                        break;
                    case 'GTE':
                        $orX->add($qb->expr()
                            ->gte('e.' . $field, ':' . $field));
                        break;
                    case 'IS_NULL':
                        $orX->add($qb->expr()
                            ->isNull('e.' . $field, ':' . $field));
                        break;
                    case 'IS_NOT_NULL':
                        $orX->add($qb->expr()
                            ->isNotNull('e.' . $field, ':' . $field));
                        break;
                    case 'IN':
                        $orX->add($qb->expr()
                            ->in('e.' . $field, ':' . $field));
                        break;
                    case 'NOT_IN':
                        // $exprs[] = $qb->expr()->isNotNull('e.' . $field, $val);
                        break;
                    case 'LIKE':
                        $orX->add($qb->expr()
                            ->like('e.' . $field, ':' . $field));
                        break;
                    case 'NOT_LIKE':
                        $orX->add($qb->expr()
                            ->notLike('e.' . $field, ':' . $field));
                        break;
                    case 'BETWEEN':
                        $orX->add(WhereBuilder::handleBetween($filter, $qb));
                        break;
                }
            }
            $andX->add($orX);
        }
        
        $qb->where($andX);
        
        foreach ($filters as $filter) {
            
            WhereBuilder::parseVal($filter);
            
            $field_array = is_array($filter->field) ? $filter->field : array(
                $filter->field
            );
            
            foreach ($field_array as $field) {
                
                
                switch ($filter->compar) {
                    case 'BETWEEN':
                        if ($filter->val['i'])
                            $qb->setParameter($field . '_i', $filter->val['i']);
                        if ($filter->val['f'])
                            $qb->setParameter($field . '_f', $filter->val['f']);
                        break;
                    case 'LIKE':
                        $qb->setParameter($field, '%' . $filter->val . '%');
                        break;
                    default:
                        $qb->setParameter($field, $filter->val);
                        break;
                }
            }
        }
    }

    private static function handleBetween($filter, $qb)
    {
        if (! $filter->val['i'] && ! $filter->val['f']) {
            return;
        }
        
        if (! $filter->val['i']) {
            return $qb->expr()->lte('e.' . $filter->field, ':' . $filter->field . '_f');
        } else if (! $filter->val['f']) {
            return $qb->expr()->gte('e.' . $filter->field, ':' . $filter->field . '_i');
        } else {
            return $qb->expr()->between('e.' . $filter->field, ':' . $filter->field . '_i', ':' . $filter->field . '_f');
        }
    }

    private static function parseVal(FilterData $filter)
    {
        if ($filter->fieldType == 'decimal') {
            if (! is_array($filter->val)) {
                $filter->val = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val);
            } else {
                if ($filter->val['i']) {
                    $filter->val['i'] = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val['i']);
                }
                if ($filter->val['f']) {
                    $filter->val['f'] = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($filter->val['f']);
                }
            }
        }
    }
}