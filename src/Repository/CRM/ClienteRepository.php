<?php

namespace App\Repository\CRM;

use App\Entity\CRM\Cliente;
use App\Repository\FilterRepository;
use App\Utils\Repository\WhereBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findByFilters($filters, $orders = null, $limit=100)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb
            ->select('e')
            ->from($this->getEntityClass(), 'e')
        ->join('App\Entity\Base\Pessoa','p','WITH','e.pessoa = p');

        WhereBuilder::build($qb, $filters);
        $dql = $qb->getDql();
        $sql = $qb->getQuery()->getSQL();
        $query = $qb->getQuery();
        $query->setMaxResults($limit);
        return $query->execute();
    }

    public function findAll($limit=100) {
        $ql = "SELECT c FROM App\Entity\CRM\Cliente c JOIN App\Entity\Base\Pessoa p WHERE c.pessoa = p";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setMaxResults($limit);
        $results = $query->getResult();
        return $results;

    }


}
