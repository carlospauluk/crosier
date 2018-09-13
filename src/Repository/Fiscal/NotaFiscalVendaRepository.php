<?php

namespace App\Repository\Fiscal;

use App\Entity\Fiscal\NotaFiscalVenda;
use App\Entity\Vendas\Venda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade NotaFiscalVenda.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class NotaFiscalVendaRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NotaFiscalVenda::class);
    }

    public function findNotaFiscalByVenda(Venda $venda)
    {
        $ql = "SELECT nf FROM App\Entity\Fiscal\NotaFiscalVenda nfv JOIN App\Entity\Fiscal\NotaFiscal nf WHERE nfv.notaFiscal = nf AND nfv.venda = :venda AND nf.ambiente = :ambiente";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'venda' => $venda,
            'ambiente' => getenv('BONSUCESSO_FISCAL_AMBIENTE')
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de uma Nota Fiscal encontrada para [' . $venda . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }
}
