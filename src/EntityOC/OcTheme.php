<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcTheme
 *
 * @ORM\Table(name="oc_theme")
 * @ORM\Entity
 */
class OcTheme
{
    /**
     * @var int
     *
     * @ORM\Column(name="theme_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $themeId;

    /**
     * @var int
     *
     * @ORM\Column(name="store_id", type="integer", nullable=false)
     */
    private $storeId;

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=64, nullable=false)
     */
    private $theme;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=64, nullable=false)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="text", length=16777215, nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
