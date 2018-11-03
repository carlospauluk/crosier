<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomFieldDescription
 *
 * @ORM\Table(name="oc_custom_field_description")
 * @ORM\Entity
 */
class OcCustomFieldDescription
{
    /**
     * @var int
     *
     * @ORM\Column(name="custom_field_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $customFieldId;

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
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;


}
