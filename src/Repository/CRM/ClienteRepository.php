<?php

namespace App\Repository\CRM;

use App\Entity\CRM\Cliente;
use App\Repository\FilterRepository;
use App\Utils\Repository\WhereBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Cliente.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ClienteRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Cliente::class;
    }

    public function findByDocumento($documento)
    {
        $ql = "SELECT c FROM App\Entity\CRM\Cliente c JOIN App\Entity\Base\Pessoa p WHERE c.pessoa = p AND p.documento = :documento";
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
        $ql = "SELECT c FROM App\Entity\CRM\Cliente c JOIN App\Entity\Base\Pessoa p WHERE c.pessoa = p AND p = :pessoa";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'pessoa' => $pessoa
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um cliente encontrado para [' . $pessoa . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->join('App\Entity\Base\Pessoa','p','WITH','e.pessoa = p');
    }

    public function findProximoCodigo() {
        $ql = "SELECT c FROM App\Entity\CRM\Cliente c ORDER BY c.codigo DESC";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setMaxResults(1);
        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um resultado encontrado');
        }

        $cliente = $results[0];
        return $cliente->getCodigo() + 1;
    }

}
