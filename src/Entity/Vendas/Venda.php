<?php
namespace App\Entity\Vendas;

use App\Entity\Base\EntityId;
use App\Entity\CRM\Cliente;
use App\Entity\RH\Funcionario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vendas\VendaRepository")
 * @ORM\Table(name="ven_venda")
 */
class Venda extends EntityId
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
     * @ORM\Column(name="desconto_especial", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'desconto_especial' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'desconto_especial' deve ser numérico")
     */
    private $descontoEspecial;

    /**
     *
     * @ORM\Column(name="desconto_plano", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'desconto_plano' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'desconto_plano' deve ser numérico")
     */
    private $descontoPlano;

    /**
     *
     * @ORM\Column(name="dt_venda", type="datetime", nullable=false)
     * @Assert\Type("\DateTime", message="O campo 'dt_venda' deve ser do tipo data/hora")
     */
    private $dtVenda;

    /**
     *
     * @ORM\Column(name="historicoDesconto", type="string", nullable=true, length=2000)
     */
    private $historicoDesconto;

    /**
     *
     * @ORM\Column(name="mesano", type="string", nullable=false, length=6)
     * @Assert\NotBlank(message="O campo 'mesano' deve ser informado")
     */
    private $mesano;

    /**
     *
     * @ORM\Column(name="pv", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $pv;

    /**
     *
     * @ORM\Column(name="sub_total", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'sub_total' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'sub_total' deve ser numérico")
     */
    private $subTotal;

    /**
     *
     * @ORM\Column(name="valor_total", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'valor_total' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'valor_total' deve ser numérico")
     */
    private $valorTotal;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vendas\PlanoPagto")
     * @ORM\JoinColumn(name="plano_pagto_id", nullable=false)
     *
     * @var $planoPagto PlanoPagto
     */
    private $planoPagto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\RH\Funcionario")
     * @ORM\JoinColumn(name="vendedor_id", nullable=false)
     *
     * @var $vendedor Funcionario
     */
    private $vendedor;

    /**
     *
     * @ORM\Column(name="deletado", type="boolean", nullable=true)
     */
    private $deletado;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vendas\TipoVenda")
     * @ORM\JoinColumn(name="tipo_venda_id", nullable=false)
     *
     * @var $tipoVenda TipoVenda
     */
    private $tipoVenda;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CRM\Cliente")
     * @ORM\JoinColumn(name="cliente_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Cliente' deve ser informado")
     *
     * @var $cliente Cliente
     */
    private $cliente;

    /**
     *
     * @ORM\Column(name="status", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'status' deve ser informado")
     */
    private $status;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=3000)
     */
    private $obs;

    /**
     *
     * @var VendaItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="VendaItem",
     *      mappedBy="venda",
     *      orphanRemoval=true)
     * @ORM\OrderBy({"ordem" = "ASC"})
     */
    private $itens;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
        $this->itens = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDescontoEspecial()
    {
        return $this->descontoEspecial;
    }

    public function setDescontoEspecial($descontoEspecial)
    {
        $this->descontoEspecial = $descontoEspecial;
    }

    public function getDescontoPlano()
    {
        return $this->descontoPlano;
    }

    public function setDescontoPlano($descontoPlano)
    {
        $this->descontoPlano = $descontoPlano;
    }

    public function getDtVenda()
    {
        return $this->dtVenda;
    }

    public function setDtVenda($dtVenda)
    {
        $this->dtVenda = $dtVenda;
    }

    public function getHistoricoDesconto()
    {
        return $this->historicoDesconto;
    }

    public function setHistoricoDesconto($historicoDesconto)
    {
        $this->historicoDesconto = $historicoDesconto;
    }

    public function getMesano()
    {
        return $this->mesano;
    }

    public function setMesano($mesano)
    {
        $this->mesano = $mesano;
    }

    public function getPv()
    {
        return $this->pv;
    }

    public function setPv($pv)
    {
        $this->pv = $pv;
    }

    public function getSubTotal()
    {
        return $this->subTotal;
    }

    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    public function getPlanoPagto(): ?PlanoPagto
    {
        return $this->planoPagto;
    }

    public function setPlanoPagto(?PlanoPagto $planoPagto)
    {
        $this->planoPagto = $planoPagto;
    }

    public function getVendedor(): ?Funcionario
    {
        return $this->vendedor;
    }

    public function setVendedor(?Funcionario $vendedor)
    {
        $this->vendedor = $vendedor;
    }

    public function getDeletado()
    {
        return $this->deletado;
    }

    public function setDeletado($deletado)
    {
        $this->deletado = $deletado;
    }

    public function getTipoVenda(): ?TipoVenda
    {
        return $this->tipoVenda;
    }

    public function setTipoVenda(?TipoVenda $tipoVenda)
    {
        $this->tipoVenda = $tipoVenda;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    /**
     *
     * @return Collection|VendaItem[]
     */
    public function getItens(): Collection
    {
        return $this->itens;
    }

    public function addItem(?VendaItem $i): void
    {
        if (! $this->itens->contains($i)) {
            $this->itens->add($i);
        }
    }
}
    