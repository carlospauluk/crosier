<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcFilter
 *
 * @ORM\Table(name="oc_filter")
 * @ORM\Entity
 */
class OcFilter
{
    /**
     * @var int
     *
     * @ORM\Column(name="filter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $filterId;

    /**
     * @var int
     *
     * @ORM\Column(name="filter_group_id", type="integer", nullable=false)
     */
    private $filterGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;


}
