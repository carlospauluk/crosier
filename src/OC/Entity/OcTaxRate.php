<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcTaxRate
 *
 * @ORM\Table(name="oc_tax_rate")
 * @ORM\Entity
 */
class OcTaxRate
{
    /**
     * @var int
     *
     * @ORM\Column(name="tax_rate_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $taxRateId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_zone_id", type="integer", nullable=false)
     */
    private $geoZoneId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="rate", type="decimal", precision=15, scale=4, nullable=false, options={"default"="0.0000"})
     */
    private $rate = '0.0000';

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1, nullable=false, options={"fixed"=true})
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false)
     */
    private $dateModified;


}
