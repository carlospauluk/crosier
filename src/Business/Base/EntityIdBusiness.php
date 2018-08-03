<?php
namespace App\Business\Base;

use App\Entity\Base\EntityId;

class EntityIdBusiness
{

    public function handlePersist(EntityId $entityId)
    {
        if (! $entityId->getId()) {
            $entityId->setInserted(new \DateTime());
            $entityId->setUserInserted(1);
        }
        $entityId->setUpdated(new \DateTime());
        $entityId->setUserUpdated(1);
        $entityId->setEstabelecimento(1);
        
        return $entityId;
    }
}