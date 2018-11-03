<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcFilterGroupDescription
 *
 * @ORM\Table(name="oc_filter_group_description")
 * @ORM\Entity
 */
class OcFilterGroupDescription
{
    /**
     * @var int
     *
     * @ORM\Column(name="filter_group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;


}
