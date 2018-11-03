<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcTranslation
 *
 * @ORM\Table(name="oc_translation")
 * @ORM\Entity
 */
class OcTranslation
{
    /**
     * @var int
     *
     * @ORM\Column(name="translation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $translationId;

    /**
     * @var int
     *
     * @ORM\Column(name="store_id", type="integer", nullable=false)
     */
    private $storeId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     */
    private $languageId;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=64, nullable=false)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=64, nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=false)
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
