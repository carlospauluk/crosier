<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\ProdutoPrecoRepository")
 * @ORM\Table(name="est_produto_preco")
 */
class ProdutoPreco extends EntityId
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="coeficiente", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'coeficiente' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'coeficiente' deve ser numérico")
     */
    private $coeficiente;

    /**
     *
     * @ORM\Column(name="custo_operacional", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'custo_operacional' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'custo_operacional' deve ser numérico")
     */
    private $custoOperacional;

    /**
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     * @Assert\Type("\DateTime", message="O campo 'dt_custo' deve ser do tipo data/hora")
     */
    private $dtCusto;

    /**
     *
     * @ORM\Column(name="dt_preco_venda", type="date", nullable=false)
     * @Assert\Type("\DateTime", message="O campo 'dt_preco_venda' deve ser do tipo data/hora")
     */
    private $dtPrecoVenda;

    /**
     *
     * @ORM\Column(name="margem", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'margem' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'margem' deve ser numérico")
     */
    private $margem;

    /**
     *
     * @ORM\Column(name="prazo", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'prazo' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $prazo;

    /**
     *
     * @ORM\Column(name="preco_custo", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'preco_custo' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'preco_custo' deve ser numérico")
     */
    private $precoCusto;

    /**
     *
     * @ORM\Column(name="preco_prazo", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'preco_prazo' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'preco_prazo' deve ser numérico")
     */
    private $precoPrazo;

    /**
     *
     * @ORM\Column(name="preco_promo", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'preco_promo' deve ser numérico")
     */
    private $precoPromo;

    /**
     *
     * @ORM\Column(name="preco_vista", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'preco_vista' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'preco_vista' deve ser numérico")
     */
    private $precoVista;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto", inversedBy="precos")
     * @ORM\JoinColumn(name="produto_id", nullable=false)
     *
     * @var $produto Produto
     */
    private $produto;

    /**
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'custo_financeiro' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'custo_financeiro' deve ser numérico")
     */
    private $custoFinanceiro;

    /**
     *
     * @ORM\Column(name="mesano", type="date", nullable=true)
     * @Assert\NotNull(message="O campo 'mesano' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'mesano' deve ser do tipo data/hora")
     */
    private $mesano;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCoeficiente()
    {
        return $this->coeficiente;
    }

    public function setCoeficiente($coeficiente)
    {
        $this->coeficiente = $coeficiente;
    }

    public function getCustoOperacional()
    {
        return $this->custoOperacional;
    }

    public function setCustoOperacional($custoOperacional)
    {
        $this->custoOperacional = $custoOperacional;
    }

    public function getDtCusto()
    {
        return $this->dtCusto;
    }

    public function setDtCusto($dtCusto)
    {
        $this->dtCusto = $dtCusto;
    }

    public function getDtPrecoVenda()
    {
        return $this->dtPrecoVenda;
    }

    public function setDtPrecoVenda($dtPrecoVenda)
    {
        $this->dtPrecoVenda = $dtPrecoVenda;
    }

    public function getMargem()
    {
        return $this->margem;
    }

    public function setMargem($margem)
    {
        $this->margem = $margem;
    }

    public function getPrazo()
    {
        return $this->prazo;
    }

    public function setPrazo($prazo)
    {
        $this->prazo = $prazo;
    }

    public function getPrecoCusto()
    {
        return $this->precoCusto;
    }

    public function setPrecoCusto($precoCusto)
    {
        $this->precoCusto = $precoCusto;
    }

    public function getPrecoPrazo()
    {
        return $this->precoPrazo;
    }

    public function setPrecoPrazo($precoPrazo)
    {
        $this->precoPrazo = $precoPrazo;
    }

    public function getPrecoPromo()
    {
        return $this->precoPromo;
    }

    public function setPrecoPromo($precoPromo)
    {
        $this->precoPromo = $precoPromo;
    }

    public function getPrecoVista()
    {
        return $this->precoVista;
    }

    public function setPrecoVista($precoVista)
    {
        $this->precoVista = $precoVista;
    }

    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function setProduto(?Produto $produto)
    {
        $this->produto = $produto;
    }

    public function getCustoFinanceiro()
    {
        return $this->custoFinanceiro;
    }

    public function setCustoFinanceiro($custoFinanceiro)
    {
        $this->custoFinanceiro = $custoFinanceiro;
    }

    public function getMesano()
    {
        return $this->mesano;
    }

    public function setMesano($mesano)
    {
        $this->mesano = $mesano;
    }

    public function getPrecoVenda() {
        return $this->getPrecoPromo() ? $this->getPrecoPromo() : $this->getPrecoPrazo();
    }
}