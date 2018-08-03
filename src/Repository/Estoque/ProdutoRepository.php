<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Produto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Produto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ProdutoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Produto::class);
    }
    
    public function findByReduzidoEktAndDtVenda($reduzidoEkt, \DateTime $dtVenda) {
        
        $ql = "SELECT p FROM App\Entity\Estoque\Produto p JOIN App\Entity\Estoque\ProdutoReduzidoEktMesano ekt WHERE ekt.produto = p AND p.reduzidoEkt = :reduzidoEkt AND ekt.mesano = :mesano";
        $query = $this->getEntityManager()->createQuery($ql);
        
        $mesano = $dtVenda->format('Ym');
        
        $query->setParameters(array(
            'reduzidoEkt' => $reduzidoEkt,
            'mesano' => $mesano
        ));
        
        $results = $query->getResult();
        
        if (count($results) > 1) {
            throw new \Exception('Mais de um produto encontrado para [' . $reduzidoEkt . '] e [' . $mesano . ']');
        }
        
        return count($results) == 1 ? $results[0] : null;
    }
}
