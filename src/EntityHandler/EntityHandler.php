<?php

namespace App\EntityHandler;


use App\Business\Base\EntityIdBusiness;
use App\Entity\Base\EntityId;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class EntityHandler
 *
 * Classe abstrata responsável pela lógica ao salvar ou deletar entidades na base de dados.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
abstract class EntityHandler
{
    private $entityManager;

    private $entityIdBusiness;

    public function __construct(RegistryInterface $doctrine, EntityIdBusiness $entityIdBusiness)
    {
        $this->entityManager = $doctrine->getEntityManager();
        $this->entityIdBusiness = $entityIdBusiness;
    }

    public function persist(EntityId $entityId)
    {
        $this->entityIdBusiness->handlePersist($entityId);
        $this->entityManager->persist($entityId);
        $this->entityManager->flush();
    }


    public function delete(EntityId $entityId)
    {
        $this->entityManager->remove($entityId);
        $this->entityManager->flush();
    }

    abstract public function getEntityClass();

}