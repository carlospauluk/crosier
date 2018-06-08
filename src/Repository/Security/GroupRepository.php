<?php
namespace App\Repository\Security;

use App\Entity\Security\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Group.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GroupRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }
}
