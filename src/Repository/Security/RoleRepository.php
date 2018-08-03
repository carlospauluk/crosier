<?php
namespace App\Repository\Security;

use App\Entity\Security\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Role.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class RoleRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Role::class);
    }
}
