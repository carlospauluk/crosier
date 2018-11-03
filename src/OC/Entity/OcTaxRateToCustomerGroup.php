<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcTaxRateToCustomerGroup
 *
 * @ORM\Table(name="oc_tax_rate_to_customer_group")
 * @ORM\Entity
 */
class OcTaxRateToCustomerGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="tax_rate_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $taxRateId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $customerGroupId;


}
