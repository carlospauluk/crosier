<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomerGroup
 *
 * @ORM\Table(name="oc_customer_group")
 * @ORM\Entity
 */
class OcCustomerGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="customer_group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customerGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="approval", type="integer", nullable=false)
     */
    private $approval;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;


}
