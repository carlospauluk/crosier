<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProduct
 *
 * @ORM\Table(name="oc_product")
 * @ORM\Entity(repositoryClass="App\Repository\OC\OcProductRepository")
 */
class OcProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=64, nullable=false)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=64, nullable=false)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="upc", type="string", length=12, nullable=false)
     */
    private $upc = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ean", type="string", length=14, nullable=false)
     */
    private $ean = '';

    /**
     * @var string
     *
     * @ORM\Column(name="jan", type="string", length=13, nullable=false)
     */
    private $jan = '';

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="string", length=17, nullable=false)
     */
    private $isbn = '';

    /**
     * @var string
     *
     * @ORM\Column(name="mpn", type="string", length=64, nullable=false)
     */
    private $mpn = '';

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=128, nullable=false)
     */
    private $location = '';

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="stock_status_id", type="integer", nullable=false)
     */
    private $stockStatusId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="manufacturer_id", type="integer", nullable=false)
     */
    private $manufacturerId;

    /**
     * @var bool
     *
     * @ORM\Column(name="shipping", type="boolean", nullable=false, options={"default"="1"})
     */
    private $shipping = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=15, scale=4, nullable=false, options={"default"="0.0000"})
     */
    private $price = '0.0000';

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="tax_class_id", type="integer", nullable=false)
     */
    private $taxClassId = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_available", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $dateAvailable = '0000-00-00';

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=15, scale=8, nullable=false, options={"default"="0.00000000"})
     */
    private $weight = '0.00000000';

    /**
     * @var int
     *
     * @ORM\Column(name="weight_class_id", type="integer", nullable=false)
     */
    private $weightClassId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="length", type="decimal", precision=15, scale=8, nullable=false, options={"default"="0.00000000"})
     */
    private $length = '0.00000000';

    /**
     * @var string
     *
     * @ORM\Column(name="width", type="decimal", precision=15, scale=8, nullable=false, options={"default"="0.00000000"})
     */
    private $width = '0.00000000';

    /**
     * @var string
     *
     * @ORM\Column(name="height", type="decimal", precision=15, scale=8, nullable=false, options={"default"="0.00000000"})
     */
    private $height = '0.00000000';

    /**
     * @var int
     *
     * @ORM\Column(name="length_class_id", type="integer", nullable=false)
     */
    private $lengthClassId = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="subtract", type="boolean", nullable=false, options={"default"="1"})
     */
    private $subtract = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="minimum", type="integer", nullable=false, options={"default"="1"})
     */
    private $minimum = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="viewed", type="integer", nullable=false)
     */
    private $viewed = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false)
     */
    private $dateModified;

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
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @return string
     */
    public function getUpc(): string
    {
        return $this->upc;
    }

    /**
     * @param string $upc
     */
    public function setUpc(string $upc): void
    {
        $this->upc = $upc;
    }

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @param string $ean
     */
    public function setEan(string $ean): void
    {
        $this->ean = $ean;
    }

    /**
     * @return string
     */
    public function getJan(): string
    {
        return $this->jan;
    }

    /**
     * @param string $jan
     */
    public function setJan(string $jan): void
    {
        $this->jan = $jan;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn(string $isbn): void
    {
        $this->isbn = $isbn;
    }

    /**
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    /**
     * @param string $mpn
     */
    public function setMpn(string $mpn): void
    {
        $this->mpn = $mpn;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getStockStatusId(): int
    {
        return $this->stockStatusId;
    }

    /**
     * @param int $stockStatusId
     */
    public function setStockStatusId(int $stockStatusId): void
    {
        $this->stockStatusId = $stockStatusId;
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
    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }

    /**
     * @param int $manufacturerId
     */
    public function setManufacturerId(int $manufacturerId): void
    {
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return bool
     */
    public function isShipping(): bool
    {
        return $this->shipping;
    }

    /**
     * @param bool $shipping
     */
    public function setShipping(bool $shipping): void
    {
        $this->shipping = $shipping;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @param int $points
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    /**
     * @return int
     */
    public function getTaxClassId(): int
    {
        return $this->taxClassId;
    }

    /**
     * @param int $taxClassId
     */
    public function setTaxClassId(int $taxClassId): void
    {
        $this->taxClassId = $taxClassId;
    }

    /**
     * @return \DateTime
     */
    public function getDateAvailable(): \DateTime
    {
        return $this->dateAvailable;
    }

    /**
     * @param \DateTime $dateAvailable
     */
    public function setDateAvailable(\DateTime $dateAvailable): void
    {
        $this->dateAvailable = $dateAvailable;
    }

    /**
     * @return string
     */
    public function getWeight(): string
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     */
    public function setWeight(string $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getWeightClassId(): int
    {
        return $this->weightClassId;
    }

    /**
     * @param int $weightClassId
     */
    public function setWeightClassId(int $weightClassId): void
    {
        $this->weightClassId = $weightClassId;
    }

    /**
     * @return string
     */
    public function getLength(): string
    {
        return $this->length;
    }

    /**
     * @param string $length
     */
    public function setLength(string $length): void
    {
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight(string $height): void
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getLengthClassId(): int
    {
        return $this->lengthClassId;
    }

    /**
     * @param int $lengthClassId
     */
    public function setLengthClassId(int $lengthClassId): void
    {
        $this->lengthClassId = $lengthClassId;
    }

    /**
     * @return bool
     */
    public function isSubtract(): bool
    {
        return $this->subtract;
    }

    /**
     * @param bool $subtract
     */
    public function setSubtract(bool $subtract): void
    {
        $this->subtract = $subtract;
    }

    /**
     * @return int
     */
    public function getMinimum(): int
    {
        return $this->minimum;
    }

    /**
     * @param int $minimum
     */
    public function setMinimum(int $minimum): void
    {
        $this->minimum = $minimum;
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

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getViewed(): int
    {
        return $this->viewed;
    }

    /**
     * @param int $viewed
     */
    public function setViewed(int $viewed): void
    {
        $this->viewed = $viewed;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded(): \DateTime
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTime $dateAdded
     */
    public function setDateAdded(\DateTime $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return \DateTime
     */
    public function getDateModified(): \DateTime
    {
        return $this->dateModified;
    }

    /**
     * @param \DateTime $dateModified
     */
    public function setDateModified(\DateTime $dateModified): void
    {
        $this->dateModified = $dateModified;
    }


}
