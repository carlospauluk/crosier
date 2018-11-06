<?php

namespace App\Entity\Estoque;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="est_produto_oc_product")
 */
class ProdutoOcProduct
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto")
     * @ORM\JoinColumn(name="est_produto_id", nullable=false)
     *
     * @var $produto Produto
     */
    private $produto;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="oc_product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @return Produto
     */
    public function getProduto(): Produto
    {
        return $this->produto;
    }

    /**
     * @param Produto $produto
     */
    public function setProduto(Produto $produto): void
    {
        $this->produto = $produto;
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


}