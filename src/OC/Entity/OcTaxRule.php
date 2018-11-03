<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcTaxRule
 *
 * @ORM\Table(name="oc_tax_rule")
 * @ORM\Entity
 */
class OcTaxRule
{
    /**
     * @var int
     *
     * @ORM\Column(name="tax_rule_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $taxRuleId;

    /**
     * @var int
     *
     * @ORM\Column(name="tax_class_id", type="integer", nullable=false)
     */
    private $taxClassId;

    /**
     * @var int
     *
     * @ORM\Column(name="tax_rate_id", type="integer", nullable=false)
     */
    private $taxRateId;

    /**
     * @var string
     *
     * @ORM\Column(name="based", type="string", length=10, nullable=false)
     */
    private $based;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default"="1"})
     */
    private $priority = '1';


}
