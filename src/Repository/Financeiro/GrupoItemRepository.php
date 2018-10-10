<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\GrupoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade GrupoItem.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class GrupoItemRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GrupoItem::class);
    }

    public function findByMesAno(\DateTime $mesAno)
    {
        $dtIni = \DateTime::createFromFormat('Y-m-d', $mesAno->format('Y-m-') . '01')->setTime(0,0,0,0);
        $dtFim = \DateTime::createFromFormat('Y-m-d', $mesAno->format('Y-m-t'))->setTime(23,59,59,999999);

        $ql = "SELECT e FROM App\Entity\Financeiro\GrupoItem e WHERE e.dtVencto BETWEEN :dtIni AND :dtFim";

        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('dtIni', $dtIni);
        $qry->setParameter('dtFim', $dtFim);
        return $qry->getResult();
    }
}
