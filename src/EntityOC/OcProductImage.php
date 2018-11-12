<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductImage
 *
 * @ORM\Table(name="oc_product_image", indexes={@ORM\Index(name="product_id", columns={"product_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\OC\OcProductImageRepository")
 */
class OcProductImage
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productImageId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder = '0';

    /**
     * @return int
     */
    public function getProductImageId(): int
    {
        return $this->productImageId;
    }

    /**
     * @param int $productImageId
     */
    public function setProductImageId(int $productImageId): void
    {
        $this->productImageId = $productImageId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return null|string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param null|string $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }


}
