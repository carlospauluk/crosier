<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade para manter registros de conferências mensais.
 *
 * @author Carlos Eduardo Pauluk
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\RegistroConferenciaRepository")
 * @ORM\Table(name="fin_reg_conf")
 */
class RegistroConferencia extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="dt_registro", type="datetime", nullable=false)
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     */
    private $dtRegistro;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_id", nullable=true)
     *
     * @var $carteira Carteira
     */
    private $carteira;

    /**
     *
     * @ORM\Column(name="valor", type="decimal", nullable=true, precision=15, scale=2)     *
     */
    private $valor;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

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

    public function getDtRegistro()
    {
        return $this->dtRegistro;
    }

    public function setDtRegistro($dtRegistro)
    {
        $this->dtRegistro = $dtRegistro;
    }

    public function getCarteira(): ?Carteira
    {
        return $this->carteira;
    }

    public function setCarteira(?Carteira $carteira)
    {
        $this->carteira = $carteira;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }
}
