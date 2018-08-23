<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Grupo;
use App\Repository\FilterRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Grupo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class GrupoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Grupo::class;
    }
}
