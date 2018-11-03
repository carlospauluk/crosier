<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcAttributeGroup
 *
 * @ORM\Table(name="oc_attribute_group")
 * @ORM\Entity
 */
class OcAttributeGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="attribute_group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $attributeGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;


}
