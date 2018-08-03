<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Fornecedor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Fornecedor.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class FornecedorRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Fornecedor::class);
    }
    
    public function findByDocumento($documento)
    {
        $ql = "SELECT f FROM App\Entity\Estoque\Fornecedor f JOIN App\Entity\Base\Pessoa p WHERE f.pessoa = p AND p.documento = :documento";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'documento' => $documento
        ));
        
        $results = $query->getResult();
        
        if (count($results) > 1) {
            throw new \Exception('Mais de um cliente encontrado para [' . $documento . ']');
        }
        
        return count($results) == 1 ? $results[0] : null;
    }
    
    public function findByPessoa($pessoa)
    {
        $ql = "SELECT f FROM App\Entity\Estoque\Fornecedor f JOIN App\Entity\Base\Pessoa p WHERE f.pessoa = p AND p = :pessoa";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'pessoa' => $pessoa
        ));
        
        $results = $query->getResult();
        
        if (count($results) > 1) {
            throw new \Exception('Mais de um fornecedor encontrado para [' . $pessoa . ']');
        }
        
        return count($results) == 1 ? $results[0] : null;
    }
}
