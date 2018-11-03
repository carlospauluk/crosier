<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductToDownload
 *
 * @ORM\Table(name="oc_product_to_download")
 * @ORM\Entity
 */
class OcProductToDownload
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="download_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $downloadId;


}
