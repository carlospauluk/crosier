<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomField
 *
 * @ORM\Table(name="oc_custom_field")
 * @ORM\Entity
 */
class OcCustomField
{
    /**
     * @var int
     *
     * @ORM\Column(name="custom_field_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customFieldId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=32, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="validation", type="string", length=255, nullable=false)
     */
    private $validation;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=10, nullable=false)
     */
    private $location;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;


}
