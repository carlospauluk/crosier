<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomerSearch
 *
 * @ORM\Table(name="oc_customer_search")
 * @ORM\Entity
 */
class OcCustomerSearch
{
    /**
     * @var int
     *
     * @ORM\Column(name="customer_search_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customerSearchId;

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
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", nullable=false)
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=false)
     */
    private $keyword;

    /**
     * @var int|null
     *
     * @ORM\Column(name="category_id", type="integer", nullable=true)
     */
    private $categoryId;

    /**
     * @var bool
     *
     * @ORM\Column(name="sub_category", type="boolean", nullable=false)
     */
    private $subCategory;

    /**
     * @var bool
     *
     * @ORM\Column(name="description", type="boolean", nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="products", type="integer", nullable=false)
     */
    private $products;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=40, nullable=false)
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
