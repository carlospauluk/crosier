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

                // Verifica se foi passado somente o nome do campo, sem o prefixo do alias da tabela
                if (strpos($field,'.') === FALSE) {
                    $field = 'e.' . $field;
                }
                // nome do parâmetro que ficará na query (tenho que trocar o '.' por '_')
                $fieldP = ':' .     str_replace('.','_',$field);
                
                switch ($filter->compar) {
                    case 'EQ':
                        $orX->add($qb->expr()
                            ->eq($field, $fieldP));
                        break;
                    case 'NEQ':
                        $orX->add($qb->expr()
                            ->neq($field, $fieldP));
                        break;
                    case 'LT':
                        $orX->add($qb->expr()
                            ->lt($field, $fieldP));
                        break;
                    case 'LTE':
                        $orX->add($qb->expr()
                            ->lte($field, $fieldP));
                        break;
                    case 'GT':
                        $orX->add($qb->expr()
                            ->gt($field, $fieldP));
                        break;
                    case 'GTE':
                        $orX->add($qb->expr()
                            ->gte($field, $fieldP));
                        break;
                    case 'IS_NULL':
                        $orX->add($qb->expr()
                            ->isNull($field, $fieldP));
                        break;
                    case 'IS_NOT_NULL':
                        $orX->add($qb->expr()
                            ->isNotNull($field, $fieldP));
                        break;
                    case 'IN':
                        $orX->add($qb->expr()
                            ->in($field, $fieldP));
                        break;
                    case 'NOT_IN':
                        // $exprs[] = $qb->expr()->isNotNull($field, $val);
                        break;
                    case 'LIKE':
                        $orX->add($qb->expr()
                            ->like($field, $fieldP));
                        break;
                    case 'NOT_LIKE':
                        $orX->add($qb->expr()
                            ->notLike($field, $fieldP));
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
            if (!$filter->val) continue;
            
            $field_array = is_array($filter->field) ? $filter->field : array(
                $filter->field
            );
            
            foreach ($field_array as $field) {

                $fieldP = str_replace('.','_',$field);
                
                switch ($filter->compar) {
                    case 'BETWEEN':
                        if ($filter->val['i'])
                            $qb->setParameter($fieldP . '_i', $filter->val['i']);
                        if ($filter->val['f'])
                            $qb->setParameter($fieldP . '_f', $filter->val['f']);
                        break;
                    case 'LIKE':
                        $qb->setParameter($fieldP, '%' . $filter->val . '%');
                        break;
                    default:
                        $qb->setParameter($fieldP, $filter->val);
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