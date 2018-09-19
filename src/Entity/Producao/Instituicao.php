<?php

namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use App\Entity\CRM\Cliente;
use App\Entity\Estoque\Fornecedor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade 'Instituição'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\InstituicaoRepository")
 * @ORM\Table(name="prod_instituicao")
 */
class Instituicao extends EntityId
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
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min = 1)
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="nome", type="string", nullable=false, length=100)
     * @Assert\NotBlank()
     */
    private $nome;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=false, length=5000)
     * @Assert\NotBlank()
     */
    private $obs;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CRM\Cliente")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var $cliente Cliente
     */
    private $cliente;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Fornecedor")
     * @ORM\JoinColumn(nullable=true)
     *
     * @var $fornecedor Fornecedor
     */
    private $fornecedor;

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

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function getFornecedor()
    {
        return $this->fornecedor;
    }

    public function setFornecedor($fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }
}

