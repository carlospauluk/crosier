<?php
namespace App\Repository\Cortinas;

use App\Entity\Cortinas\Tecido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Tecido.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class TecidoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tecido::class);
    }
}
