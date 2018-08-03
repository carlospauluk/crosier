<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoPreco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade ProdutoPreco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ProdutoPrecoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProdutoPreco::class);
    }
    
    public function findPrecoEmDataVenda(Produto $produto, $dtVenda) {
        $ql = "SELECT pp FROM App\Entity\Estoque\ProdutoPreco pp JOIN App\Entity\Estoque\Produto p WHERE pp.produto = p AND pp.dtPrecoVenda >= :dtVenda ORDER BY pp.dtPrecoVenda DESC";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'produto' => $produto,
            'dtVenda' => $dtVenda
        ));
        
        $results = $query->getResult();
        return count($results) >= 1 ? $results[0] : null;
    }
}
