<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomerApproval
 *
 * @ORM\Table(name="oc_customer_approval")
 * @ORM\Entity
 */
class OcCustomerApproval
{
    /**
     * @var int
     *
     * @ORM\Column(name="customer_approval_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customerApprovalId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", nullable=false)
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=9, nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
