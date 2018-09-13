<?php

namespace App\Entity\Fiscal;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * Entidade que guarda informações sobre o histórico da nota fiscal.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Fiscal\NotaFiscalHistoricoRepository")
 * @ORM\Table(name="fis_nf_historico")
 *
 * @author Carlos Eduardo Pauluk
 */
class NotaFiscalHistorico extends EntityId
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Fiscal\NotaFiscal")
     * @ORM\JoinColumn(name="fis_nf_id", nullable=false)
     *
     * @var $fisNf NotaFiscal
     */
    private $notaFiscal;

    /**
     *
     * @ORM\Column(name="codigo_status", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'codigo_status' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $codigoStatus;

    /**
     *
     * @ORM\Column(name="dt_historico", type="datetime", nullable=false)
     * @Assert\NotNull(message="O campo 'dt_historico' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_historico' deve ser do tipo data/hora")
     */
    private $dtHistorico;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=2000)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=false, length=255)
     * @Assert\NotBlank(message="O campo 'historico' deve ser informado")
     */
    private $obs;

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

    public function getNotaFiscal()
    {
        return $this->notaFiscal;
    }

    public function setNotaFiscal($notaFiscal)
    {
        $this->notaFiscal = $notaFiscal;
    }

    public function getCodigoStatus()
    {
        return $this->codigoStatus;
    }

    public function setCodigoStatus($codigoStatus)
    {
        $this->codigoStatus = $codigoStatus;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDtHistorico(): ?\DateTime
    {
        return $this->dtHistorico;
    }

    public function setDtHistorico(?\DateTime $dtHistorico)
    {
        $this->dtHistorico = $dtHistorico;
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