<?php

namespace App\Repository\RH;

use App\Entity\RH\Funcionario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Funcionario.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FuncionarioRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Funcionario::class);
    }

    public function findByCodigo($codigo)
    {
        $ql = "SELECT f FROM App\Entity\RH\Funcionario f JOIN App\Entity\RH\FuncionarioCargo fc WHERE fc.funcionario = f AND f.codigo = :codigo AND fc.atual = TRUE";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'codigo' => $codigo
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um funcionario encontrado para [' . $codigo . '] com atual = true');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    public function findByPessoa($pessoa)
    {
        $ql = "SELECT f FROM App\Entity\RH\Funcionario f JOIN App\Entity\Base\Pessoa p WHERE f.pessoa = p AND p = :pessoa";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'pessoa' => $pessoa
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um funcionário encontrado para [' . $pessoa . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }
}
