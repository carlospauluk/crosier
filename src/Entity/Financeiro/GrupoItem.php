<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Entidade que representa um item de um Grupo de Movimentações (como a fatura
 * de um mês do cartão de crédito, por exemplo).
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\GrupoRepository")
 * @ORM\Table(name="fin_grupo")
 *
 * @author Carlos Eduardo Pauluk
 */
class GrupoItem extends EntityId
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Grupo")
     * @ORM\JoinColumn(nullable=true)
     */
    private $pai;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=40)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     * Movimentações desta carteira não poderão ter suas datas alteradas para antes desta.
     *
     * @ORM\Column(name="dt_vencto", type="date", nullable=false)
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     */
    private $dtVencto;

    /**
     * Para efeitos de navegação.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Financeiro\GrupoItem")
     * @ORM\JoinColumn(name="anterior_id", referencedColumnName="id")
     */
    private $anterior;

    /**
     * Para efeitos de navegação.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Financeiro\GrupoItem")
     * @ORM\JoinColumn(name="proximo_id", referencedColumnName="id")
     */
    private $proximo;

    /**
     * Utilizado para informar o limite disponível.
     *
     * @ORM\Column(name="valor_informado", type="decimal", nullable=true, precision=15, scale=2)
     */
    private $valorInformado;

    /**
     *
     * @var Movimentacao[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Movimentacao", mappedBy="grupoItem")
     */
    private $movimentacoes;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_pagante_id", nullable=true)
     */
    private $carteiraPagante;

    /**
     *
     * @ORM\Column(name="caixa", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $fechado = false;

    /**
     */
    public function __construct()
    {
        $this->movimentacoes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPai()
    {
        return $this->pai;
    }

    public function setPai($pai)
    {
        $this->pai = $pai;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDtVencto()
    {
        return $this->dtVencto;
    }

    public function setDtVencto($dtVencto)
    {
        $this->dtVencto = $dtVencto;
    }

    public function getAnterior()
    {
        return $this->anterior;
    }

    public function setAnterior($anterior)
    {
        $this->anterior = $anterior;
    }

    public function getProximo()
    {
        return $this->proximo;
    }

    public function setProximo($proximo)
    {
        $this->proximo = $proximo;
    }

    public function getValorInformado()
    {
        return $this->valorInformado;
    }

    public function setValorInformado($valorInformado)
    {
        $this->valorInformado = $valorInformado;
    }

    /**
     *
     * @return Collection|Movimentacao[]
     */
    public function getMovimentacoes(): Collection
    {
        return $this->movimentacoes;
    }

    public function setMovimentacoes($movimentacoes)
    {
        $this->movimentacoes = $movimentacoes;
    }

    public function getCarteiraPagante(): ?Carteira
    {
        return $this->carteiraPagante;
    }

    public function setCarteiraPagante(?Carteira $carteiraPagante)
    {
        $this->carteiraPagante = $carteiraPagante;
    }

    public function getFechado()
    {
        return $this->fechado;
    }

    public function setFechado($fechado)
    {
        $this->fechado = $fechado;
    }

    public function getValorLanctos()
    {
        if (($this->getMovimentacoes() != null) && (count($this->getMovimentacoes()) > 0)) {
            $bdValor = 0.0;
            foreach ($this->getMovimentacoes as $m) {
                if (substr($m->getCategoria()->getCodigo(), 0, 1) == "1") {
                    $bdValor += $m->getValorTotal();
                } else {
                    $bdValor -= $m->getValorTotal();
                }
            }
        }
        
        return abs($bdValor);
    }

    public function getDiferenca()
    {
        return $this->getValorLanctos() - $this->getValorInformado();
    }
}

