<?php

namespace App\Repository\Fiscal;

use App\Entity\Fiscal\NotaFiscal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade NotaFiscalHistorico.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class NotaFiscalHistoricoRepository extends ServiceEntityRepository
{

    private $logger;

    public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, NotaFiscal::class);
        $this->getLogger = $logger;
    }
}
