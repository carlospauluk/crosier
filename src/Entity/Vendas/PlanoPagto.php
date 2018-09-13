<?php

namespace App\Entity\Vendas;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vendas\PlanoPagtoRepository")
 * @ORM\Table(name="ven_plano_pagto")
 */
class PlanoPagto extends EntityId
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
     * @ORM\Column(name="codigo", type="string", nullable=false, length=255)
     * @Assert\NotBlank(message="O campo 'codigo' deve ser informado")
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=255)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

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

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }
}