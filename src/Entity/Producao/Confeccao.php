<?php
namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Estoque\Grade;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\ConfeccaoRepository")
 * @ORM\Table(name="prod_confeccao")
 */
class Confeccao extends EntityId
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
     * @ORM\Column(name="bloqueada", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'bloqueada' deve ser informado")
     */
    private $bloqueada;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\Column(name="prazo_padrao", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'prazo_padrao' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $prazoPadrao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\Instituicao")
     * @ORM\JoinColumn(name="instituicao_id", nullable=false)
     *
     * @var $instituicao Instituicao
     */
    private $instituicao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\TipoArtigo")
     * @ORM\JoinColumn(name="tipo_artigo_id", nullable=false)
     *
     * @var $tipoArtigo TipoArtigo
     */
    private $tipoArtigo;

    /**
     *
     * @ORM\Column(name="custo_financeiro_padrao", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'custo_financeiro_padrao' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'custo_financeiro_padrao' deve ser numérico")
     */
    private $custoFinanceiroPadrao;

    /**
     *
     * @ORM\Column(name="modo_calculo", type="string", nullable=false, length=15)
     * @Assert\NotBlank(message="O campo 'modo_calculo' deve ser informado")
     */
    private $modoCalculo;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Grade")
     * @ORM\JoinColumn(name="grade_id", nullable=false)
     *
     * @var $grade Grade
     */
    private $grade;

    /**
     *
     * @ORM\Column(name="oculta", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'oculta' deve ser informado")
     */
    private $oculta;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
        Grade::class;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getBloqueada()
    {
        return $this->bloqueada;
    }

    public function setBloqueada($bloqueada)
    {
        $this->bloqueada = $bloqueada;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getPrazoPadrao()
    {
        return $this->prazoPadrao;
    }

    public function setPrazoPadrao($prazoPadrao)
    {
        $this->prazoPadrao = $prazoPadrao;
    }

    public function getInstituicao()
    {
        return $this->instituicao;
    }

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }

    public function getTipoArtigo()
    {
        return $this->tipoArtigo;
    }

    public function setTipoArtigo($tipoArtigo)
    {
        $this->tipoArtigo = $tipoArtigo;
    }

    public function getCustoFinanceiroPadrao()
    {
        return $this->custoFinanceiroPadrao;
    }

    public function setCustoFinanceiroPadrao($custoFinanceiroPadrao)
    {
        $this->custoFinanceiroPadrao = $custoFinanceiroPadrao;
    }

    public function getModoCalculo()
    {
        return $this->modoCalculo;
    }

    public function setModoCalculo($modoCalculo)
    {
        $this->modoCalculo = $modoCalculo;
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    public function getOculta()
    {
        return $this->oculta;
    }

    public function setOculta($oculta)
    {
        $this->oculta = $oculta;
    }
}