<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomFieldValueDescription
 *
 * @ORM\Table(name="oc_custom_field_value_description")
 * @ORM\Entity
 */
class OcCustomFieldValueDescription
{
    /**
     * @var int
     *
     * @ORM\Column(name="custom_field_value_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $customFieldValueId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageId;

    /**
     * @var int
     *
     * @ORM\Column(name="custom_field_id", type="integer", nullable=false)
     */
    private $customFieldId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;


}
