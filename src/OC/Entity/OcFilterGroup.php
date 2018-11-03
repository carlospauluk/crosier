<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcFilterGroup
 *
 * @ORM\Table(name="oc_filter_group")
 * @ORM\Entity
 */
class OcFilterGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="filter_group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $filterGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;


}
