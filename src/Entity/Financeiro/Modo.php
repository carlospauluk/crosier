<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade Modo de Movimentação.
 *
 * Informa se a movimentação foi em 'espécie', 'cheque', 'boleto', etc.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\ModoRepository")
 * @ORM\Table(name="fin_modo")
 *
 * @author Carlos Eduardo Pauluk
 */
class Modo extends EntityId
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
     * @Assert\Range(min=1)
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=40)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     * Informa se este modo é aceito para transferências próprias (entre
     * carteiras).
     *
     * @ORM\Column(name="transf_propria", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $modoDeTransfPropria = false;

    /**
     * Informa se este modo é aceito para transferências próprias (entre
     * carteiras).
     *
     * @ORM\Column(name="moviment_agrup", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $modoDeMovimentAgrup = false;

    /**
     * Informa se este modo é aceito para transferências próprias (entre
     * carteiras).
     *
     * @ORM\Column(name="modo_cartao", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $modoDeCartao = false;

    /**
     * Informa se este modo é aceito para transferências próprias (entre
     * carteiras).
     *
     * @ORM\Column(name="modo_cheque", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $modoDeCheque = false;

    /**
     * Informa se este modo é aceito para transferência/recolhimento de caixas.
     *
     * @ORM\Column(name="transf_caixa", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $modoDeTransfCaixa = false;

    /**
     *
     * @ORM\Column(name="com_banco_origem", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $modoComBancoOrigem = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 2, "0", STR_PAD_LEFT);
        } else {
            return $this->codigo;
        }
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

    public function getDescricaoMontada()
    {
        return $this->getCodigo(true) . ' - ' . $this->getDescricao();
    }

    public function getModoDeTransfPropria()
    {
        return $this->modoDeTransfPropria;
    }

    public function setModoDeTransfPropria($modoDeTransfPropria)
    {
        $this->modoDeTransfPropria = $modoDeTransfPropria;
    }

    public function getModoDeMovimentAgrup()
    {
        return $this->modoDeMovimentAgrup;
    }

    public function setModoDeMovimentAgrup($modoDeMovimentAgrup)
    {
        $this->modoDeMovimentAgrup = $modoDeMovimentAgrup;
    }

    public function getModoDeCartao()
    {
        return $this->modoDeCartao;
    }

    public function setModoDeCartao($modoDeCartao)
    {
        $this->modoDeCartao = $modoDeCartao;
    }

    public function getModoDeCheque()
    {
        return $this->modoDeCheque;
    }

    public function setModoDeCheque($modoDeCheque)
    {
        $this->modoDeCheque = $modoDeCheque;
    }

    public function getModoDeTransfCaixa()
    {
        return $this->modoDeTransfCaixa;
    }

    public function setModoDeTransfCaixa($modoDeTransfCaixa)
    {
        $this->modoDeTransfCaixa = $modoDeTransfCaixa;
    }

    public function getModoComBancoOrigem()
    {
        return $this->modoComBancoOrigem;
    }

    public function setModoComBancoOrigem($modoComBancoOrigem)
    {
        $this->modoComBancoOrigem = $modoComBancoOrigem;
    }
}
