<?php

namespace App\EntityHandler;


use App\Business\Base\EntityIdBusiness;
use App\Entity\Base\EntityId;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class EntityHandler
 *
 * Classe abstrata responsável pela lógica ao salvar ou deletar entidades na base de dados.
 * FIXME: se algum dia o PHP suportar herança para tipagem de parâmetros em métodos, adicionar os tipos nos before's e after's aqui.
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
abstract class EntityHandler
{
    protected $entityManager;

    private $security;

    public function __construct(RegistryInterface $doctrine, Security $security)
    {
        $this->entityManager = $doctrine->getEntityManager();
        $this->security = $security;
    }

    public function beforePersist($entityId)
    {
    }

    public function persist(EntityId $entityId)
    {
        $this->beforePersist($entityId);
        $this->entityManager->persist($entityId);
        $this->entityManager->flush();
        $this->afterPersist($entityId);
        return $entityId;
    }

    public function afterPersist($entityId)
    {
    }

    public function beforeDelete($entityId)
    {
    }

    public function delete($entityId)
    {
        $this->beforeDelete($entityId);
        $this->entityManager->remove($entityId);
        $this->entityManager->flush();
        $this->afterDelete($entityId);
    }

    public function afterDelete($entityId)
    {
    }

    public function beforeClone($entityId)
    {
    }

    public function doClone($e) {
        $newE = clone $e;
        $newE->setId(null);
        $this->beforeClone($newE);
        $newE = $this->persist($newE);
        $this->getEntityManager()->flush($newE);
        return $newE;
    }

    abstract public function getEntityClass();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(): \Doctrine\ORM\EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

}