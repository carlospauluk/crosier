<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\RegistroConferencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade RegistroConferencia.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class RegistroConferenciaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistroConferencia::class);
    }
}
            