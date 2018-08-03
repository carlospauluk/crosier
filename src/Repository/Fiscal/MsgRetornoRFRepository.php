<?php
namespace App\Repository\Fiscal;

use App\Entity\Fiscal\MsgRetornoRF;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade MsgRetornoRF.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class MsgRetornoRFRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MsgRetornoRF::class);
    }
}
