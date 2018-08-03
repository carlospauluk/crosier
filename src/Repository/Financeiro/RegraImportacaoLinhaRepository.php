<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\RegraImportacaoLinha;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade RegraImportacaoLinha.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class RegraImportacaoLinhaRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RegraImportacaoLinha::class);
    }
}
                        