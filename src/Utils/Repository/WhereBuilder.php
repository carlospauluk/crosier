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
            
            switch ($filter->compar) {
                case 'EQ':
                    $andX->add($qb->expr()
                        ->eq('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'NEQ':
                    $andX->add($qb->expr()
                        ->neq('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'LT':
                    $andX->add($qb->expr()
                        ->lt('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'LTE':
                    $andX->add($qb->expr()
                        ->lte('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'GT':
                    $andX->add($qb->expr()
                        ->gt('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'GTE':
                    $andX->add($qb->expr()
                        ->gte('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'IS_NULL':
                    $andX->add($qb->expr()
                        ->isNull('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'IS_NOT_NULL':
                    $andX->add($qb->expr()
                        ->isNotNull('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'IN':
                    // $exprs[] = $qb->expr()->isNotNull('e.' . $filter->field, $filter->val);
                    break;
                case 'NOT_IN':
                    // $exprs[] = $qb->expr()->isNotNull('e.' . $filter->field, $filter->val);
                    break;
                case 'LIKE':
                    $andX->add($qb->expr()
                        ->like('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'NOT_LIKE':
                    $andX->add($qb->expr()
                        ->notLike('e.' . $filter->field, ':' . $filter->field));
                    break;
                case 'BETWEEN':
                    $andX->add(WhereBuilder::handleBetween($filter, $qb));
                    break;
            }
        }
        
        $qb->where($andX);
        
        foreach ($filters as $filter) {
            
            WhereBuilder::parseVal($filter);
            
            switch ($filter->compar) {
                case 'BETWEEN':
                    if ($filter->val['i'])
                        $qb->setParameter($filter->field . '_i', $filter->val['i']);
                    if ($filter->val['f'])
                        $qb->setParameter($filter->field . '_f', $filter->val['f']);
                    break;
                case 'LIKE':
                    $qb->setParameter($filter->field, '%' . $filter->val . '%');
                    break;
                default:
                    $qb->setParameter($filter->field, $filter->val);
                    break;
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
    
    private static function parseVal(FilterData $filter) {
        if ($filter->fieldType == 'decimal') {
            if (!is_array($filter->val)) {
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