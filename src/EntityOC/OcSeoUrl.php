<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcSeoUrl
 *
 * @ORM\Table(name="oc_seo_url", indexes={@ORM\Index(name="query", columns={"query"}), @ORM\Index(name="keyword", columns={"keyword"})})
 * @ORM\Entity
 */
class OcSeoUrl
{
    /**
     * @var int
     *
     * @ORM\Column(name="seo_url_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $seoUrlId;

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
     * @ORM\Column(name="query", type="string", length=255, nullable=false)
     */
    private $query;

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=false)
     */
    private $keyword;


}
