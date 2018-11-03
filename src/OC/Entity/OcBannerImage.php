<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcBannerImage
 *
 * @ORM\Table(name="oc_banner_image")
 * @ORM\Entity
 */
class OcBannerImage
{
    /**
     * @var int
     *
     * @ORM\Column(name="banner_image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bannerImageId;

    /**
     * @var int
     *
     * @ORM\Column(name="banner_id", type="integer", nullable=false)
     */
    private $bannerId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     */
    private $languageId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=false)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder = '0';


}
