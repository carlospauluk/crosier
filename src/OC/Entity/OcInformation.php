<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcInformation
 *
 * @ORM\Table(name="oc_information")
 * @ORM\Entity
 */
class OcInformation
{
    /**
     * @var int
     *
     * @ORM\Column(name="information_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $informationId;

    /**
     * @var int
     *
     * @ORM\Column(name="bottom", type="integer", nullable=false)
     */
    private $bottom = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false, options={"default"="1"})
     */
    private $status = '1';


}
