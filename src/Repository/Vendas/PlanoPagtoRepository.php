<?php

namespace App\Repository\Vendas;

use App\Entity\Vendas\PlanoPagto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade PlanoPagto.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PlanoPagtoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanoPagto::class);
    }

    public function findByDescricao($descricao)
    {
        $ql = "SELECT pp FROM App\Entity\Vendas\PlanoPagto pp WHERE pp.descricao = :descricao";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'descricao' => $descricao
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um plano de pagto encontrado para [' . $descricao . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }
}
