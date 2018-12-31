<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade 'Grupo de Movimentações'.
 *
 * Para movimentações que são agrupadas e pagas através de outra movimentação (como Cartão de Crédito, conta em postos, etc).
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\GrupoRepository")
 * @ORM\Table(name="fin_grupo")
 */
class Grupo extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=40)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     * Dia de vencimento no mês.
     *
     * 32 para sempre último (FIXME: meio burro isso).
     *
     * @ORM\Column(name="dia_vencto", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 32)
     */
    private $diaVencto;

    /**
     * Dia a partir do qual as movimentações são consideradas com vencimento
     * para próximo mês.
     *
     * @ORM\Column(name="dia_inicio", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 31)
     */
    private $diaInicioAprox = 1;

    /**
     * Informa se esta carteira pode conter movimentações com status ABERTA.
     * útil principalmente para o relatório de contas a pagar/receber, para não considerar movimentações de outras carteiras.
     *
     * @ORM\Column(name="ativo", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $ativo = true;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_pagante_id", nullable=true)
     *
     * @var $carteiraPagantePadrao Carteira
     */
    private $carteiraPagantePadrao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Categoria")
     * @ORM\JoinColumn(name="categoria_padrao_id", nullable=true)
     *
     * @var $categoriaPadrao Categoria
     */
    private $categoriaPadrao;

    /**
     *
     * @var GrupoItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="GrupoItem",
     *      mappedBy="pai",
     *      orphanRemoval=true
     * )
     */
    private $itens;

    public function __construct()
    {
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

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDiaVencto()
    {
        return $this->diaVencto;
    }

    public function setDiaVencto($diaVencto)
    {
        $this->diaVencto = $diaVencto;
    }

    public function getDiaInicioAprox()
    {
        return $this->diaInicioAprox;
    }

    public function setDiaInicioAprox($diaInicioAprox)
    {
        $this->diaInicioAprox = $diaInicioAprox;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function getCarteiraPagantePadrao(): ?Carteira
    {
        return $this->carteiraPagantePadrao;
    }

    public function setCarteiraPagantePadrao(?Carteira $carteiraPagantePadrao)
    {
        $this->carteiraPagantePadrao = $carteiraPagantePadrao;
    }

    public function getCategoriaPadrao(): ?Categoria
    {
        return $this->categoriaPadrao;
    }

    public function setCategoriaPadrao(?Categoria $categoriaPadrao)
    {
        $this->categoriaPadrao = $categoriaPadrao;
    }


    /**
     *
     * @return Collection|Categoria[]
     */
    public function getItens(): Collection
    {
        return $this->itens;
    }

}

