<?php

namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use App\Entity\Estoque\Fornecedor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\InsumoPrecoRepository")
 * @ORM\Table(name="prod_insumo_preco")
 */
class InsumoPreco extends EntityId
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
     * @ORM\Column(name="coeficiente", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'coeficiente' deve ser numérico")
     */
    private $coeficiente;

    /**
     *
     * @ORM\Column(name="custo_operacional", type="decimal", nullable=true, precision=15, scale=2)
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
     * @ORM\Column(name="margem", type="decimal", nullable=true, precision=15, scale=2)
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
     * @ORM\Column(name="preco_custo", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'preco_custo' deve ser numérico")
     */
    private $precoCusto;

    /**
     *
     * @ORM\Column(name="preco_prazo", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'preco_prazo' deve ser numérico")
     */
    private $precoPrazo;

    /**
     *
     * @ORM\Column(name="preco_vista", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'preco_vista' deve ser numérico")
     */
    private $precoVista;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Fornecedor")
     * @ORM\JoinColumn(name="fornecedor_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Fornecedor' deve ser informado")
     *
     * @var $fornecedor Fornecedor
     */
    private $fornecedor;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\Insumo")
     * @ORM\JoinColumn(name="insumo_id", nullable=false)
     *
     * @var $insumo Insumo
     */
    private $insumo;

    /**
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'custo_financeiro' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'custo_financeiro' deve ser numérico")
     */
    private $custoFinanceiro;

    /**
     *
     * @ORM\Column(name="atual", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'atual' deve ser informado")
     */
    private $atual;

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

    public function getPrecoVista()
    {
        return $this->precoVista;
    }

    public function setPrecoVista($precoVista)
    {
        $this->precoVista = $precoVista;
    }

    public function getFornecedor(): ?Fornecedor
    {
        return $this->fornecedor;
    }

    public function setFornecedor(?Fornecedor $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function getInsumo(): ?Insumo
    {
        return $this->insumo;
    }

    public function setInsumo(?Insumo $insumo)
    {
        $this->insumo = $insumo;
    }

    public function getCustoFinanceiro()
    {
        return $this->custoFinanceiro;
    }

    public function setCustoFinanceiro($custoFinanceiro)
    {
        $this->custoFinanceiro = $custoFinanceiro;
    }

    public function getAtual()
    {
        return $this->atual;
    }

    public function setAtual($atual)
    {
        $this->atual = $atual;
    }
}
