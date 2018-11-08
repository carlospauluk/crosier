<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductOptionValue
 *
 * @ORM\Table(name="oc_product_option_value")
 * @ORM\Entity(repositoryClass="App\Repository\OC\OcProductOptionValueRepository")
 */
class OcProductOptionValue
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_option_value_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productOptionValueId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_option_id", type="integer", nullable=false)
     */
    private $productOptionId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="option_id", type="integer", nullable=false)
     */
    private $optionId;

    /**
     * @var int
     *
     * @ORM\Column(name="option_value_id", type="integer", nullable=false)
     */
    private $optionValueId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var bool
     *
     * @ORM\Column(name="subtract", type="boolean", nullable=false)
     */
    private $subtract;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=15, scale=4, nullable=false)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="price_prefix", type="string", length=1, nullable=false)
     */
    private $pricePrefix;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points;

    /**
     * @var string
     *
     * @ORM\Column(name="points_prefix", type="string", length=1, nullable=false)
     */
    private $pointsPrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=15, scale=8, nullable=false)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="weight_prefix", type="string", length=1, nullable=false)
     */
    private $weightPrefix;

    /**
     * @return int
     */
    public function getProductOptionValueId(): ?int
    {
        return $this->productOptionValueId;
    }

    /**
     * @param int $productOptionValueId
     */
    public function setProductOptionValueId(int $productOptionValueId): void
    {
        $this->productOptionValueId = $productOptionValueId;
    }

    /**
     * @return int
     */
    public function getProductOptionId(): int
    {
        return $this->productOptionId;
    }

    /**
     * @param int $productOptionId
     */
    public function setProductOptionId(int $productOptionId): void
    {
        $this->productOptionId = $productOptionId;
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
     * @return int
     */
    public function getOptionId(): int
    {
        return $this->optionId;
    }

    /**
     * @param int $optionId
     */
    public function setOptionId(int $optionId): void
    {
        $this->optionId = $optionId;
    }

    /**
     * @return int
     */
    public function getOptionValueId(): int
    {
        return $this->optionValueId;
    }

    /**
     * @param int $optionValueId
     */
    public function setOptionValueId(int $optionValueId): void
    {
        $this->optionValueId = $optionValueId;
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
     * @return string
     */
    public function getPricePrefix(): string
    {
        return $this->pricePrefix;
    }

    /**
     * @param string $pricePrefix
     */
    public function setPricePrefix(string $pricePrefix): void
    {
        $this->pricePrefix = $pricePrefix;
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
     * @return string
     */
    public function getPointsPrefix(): string
    {
        return $this->pointsPrefix;
    }

    /**
     * @param string $pointsPrefix
     */
    public function setPointsPrefix(string $pointsPrefix): void
    {
        $this->pointsPrefix = $pointsPrefix;
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
     * @return string
     */
    public function getWeightPrefix(): string
    {
        return $this->weightPrefix;
    }

    /**
     * @param string $weightPrefix
     */
    public function setWeightPrefix(string $weightPrefix): void
    {
        $this->weightPrefix = $weightPrefix;
    }


}
