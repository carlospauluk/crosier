<?php
namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Cortinas\ArtigoCortina;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\ProdutoRepository")
 * @ORM\Table(name="est_produto")
 * @ORM\HasLifecycleCallbacks()
 */
class Produto extends EntityId
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
     * @ORM\Column(name="cst", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'cst' deve ser informado")
     */
    private $cst;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="dt_ult_venda", type="date", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_ult_venda' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_ult_venda' deve ser do tipo data/hora")
     */
    private $dtUltVenda;

    /**
     *
     * @ORM\Column(name="fracionado", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'fracionado' deve ser informado")
     */
    private $fracionado;

    /**
     *
     * @ORM\Column(name="grade_err", type="string", nullable=true, length=200)
     */
    private $gradeErr;

    /**
     *
     * @ORM\Column(name="icms", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'icms' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $icms;

    /**
     *
     * @ORM\Column(name="ncm", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'ncm' deve ser informado")
     */
    private $ncm;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\Column(name="reduzido", type="bigint", nullable=false)
     * @Assert\NotBlank(message="O campo 'reduzido' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $reduzido;

    /**
     *
     * @ORM\Column(name="reduzido_ekt", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $reduzidoEkt;

    /**
     *
     * @ORM\Column(name="reduzido_ekt_ate", type="date", nullable=true)
     * @Assert\NotNull(message="O campo 'reduzido_ekt_ate' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'reduzido_ekt_ate' deve ser do tipo data/hora")
     */
    private $reduzidoEktAte;

    /**
     *
     * @ORM\Column(name="reduzido_ekt_desde", type="date", nullable=true)
     * @Assert\NotNull(message="O campo 'reduzido_ekt_desde' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'reduzido_ekt_desde' deve ser do tipo data/hora")
     */
    private $reduzidoEktDesde;

    /**
     *
     * @ORM\Column(name="referencia", type="string", nullable=false, length=20)
     * @Assert\NotBlank(message="O campo 'referencia' deve ser informado")
     */
    private $referencia;

    /**
     *
     * @ORM\Column(name="subdepto_err", type="string", nullable=true, length=200)
     */
    private $subdeptoErr;

    /**
     *
     * @ORM\Column(name="tipo_tributacao", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'tipo_tributacao' deve ser informado")
     */
    private $tipoTributacao;

    /**
     *
     * @ORM\Column(name="unidade_produto_err", type="string", nullable=true, length=200)
     */
    private $unidadeProdutoErr;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Depto")
     * @ORM\JoinColumn(name="depto_imp_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Depto_imp' deve ser informado")
     *
     * @var $deptoImp Depto
     */
    private $deptoImp;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Fornecedor")
     * @ORM\JoinColumn(name="fornecedor_id", nullable=false)
     *
     * @var $fornecedor Fornecedor
     */
    private $fornecedor;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Subdepto")
     * @ORM\JoinColumn(name="subdepto_id", nullable=false)
     *
     * @var $subdepto Subdepto
     */
    private $subdepto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\UnidadeProduto")
     * @ORM\JoinColumn(name="unidade_produto_id", nullable=false)
     *
     * @var $unidadeProduto UnidadeProduto
     */
    private $unidadeProduto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Cortinas\ArtigoCortina")
     * @ORM\JoinColumn(name="artigo_cortina_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Artigo_cortina' deve ser informado")
     *
     * @var $artigoCortina ArtigoCortina
     */
    private $artigoCortina;

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

    public function getCst()
    {
        return $this->cst;
    }

    public function setCst($cst)
    {
        $this->cst = $cst;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDtUltVenda()
    {
        return $this->dtUltVenda;
    }

    public function setDtUltVenda($dtUltVenda)
    {
        $this->dtUltVenda = $dtUltVenda;
    }

    public function getFracionado()
    {
        return $this->fracionado;
    }

    public function setFracionado($fracionado)
    {
        $this->fracionado = $fracionado;
    }

    public function getGradeErr()
    {
        return $this->gradeErr;
    }

    public function setGradeErr($gradeErr)
    {
        $this->gradeErr = $gradeErr;
    }

    public function getIcms()
    {
        return $this->icms;
    }

    public function setIcms($icms)
    {
        $this->icms = $icms;
    }

    public function getNcm()
    {
        return $this->ncm;
    }

    public function setNcm($ncm)
    {
        $this->ncm = $ncm;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getReduzido()
    {
        return $this->reduzido;
    }

    public function setReduzido($reduzido)
    {
        $this->reduzido = $reduzido;
    }

    public function getReduzidoEkt()
    {
        return $this->reduzidoEkt;
    }

    public function setReduzidoEkt($reduzidoEkt)
    {
        $this->reduzidoEkt = $reduzidoEkt;
    }

    public function getReduzidoEktAte()
    {
        return $this->reduzidoEktAte;
    }

    public function setReduzidoEktAte($reduzidoEktAte)
    {
        $this->reduzidoEktAte = $reduzidoEktAte;
    }

    public function getReduzidoEktDesde()
    {
        return $this->reduzidoEktDesde;
    }

    public function setReduzidoEktDesde($reduzidoEktDesde)
    {
        $this->reduzidoEktDesde = $reduzidoEktDesde;
    }

    public function getReferencia()
    {
        return $this->referencia;
    }

    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
    }

    public function getSubdeptoErr()
    {
        return $this->subdeptoErr;
    }

    public function setSubdeptoErr($subdeptoErr)
    {
        $this->subdeptoErr = $subdeptoErr;
    }

    public function getTipoTributacao()
    {
        return $this->tipoTributacao;
    }

    public function setTipoTributacao($tipoTributacao)
    {
        $this->tipoTributacao = $tipoTributacao;
    }

    public function getUnidadeProdutoErr()
    {
        return $this->unidadeProdutoErr;
    }

    public function setUnidadeProdutoErr($unidadeProdutoErr)
    {
        $this->unidadeProdutoErr = $unidadeProdutoErr;
    }

    public function getDeptoImp()
    {
        return $this->deptoImp;
    }

    public function setDeptoImp($deptoImp)
    {
        $this->deptoImp = $deptoImp;
    }

    public function getFornecedor()
    {
        return $this->fornecedor;
    }

    public function setFornecedor($fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    public function getSubdepto()
    {
        return $this->subdepto;
    }

    public function setSubdepto($subdepto)
    {
        $this->subdepto = $subdepto;
    }

    public function getUnidadeProduto()
    {
        return $this->unidadeProduto;
    }

    public function setUnidadeProduto($unidadeProduto)
    {
        $this->unidadeProduto = $unidadeProduto;
    }

    public function getArtigoCortina()
    {
        return $this->artigoCortina;
    }

    public function setArtigoCortina($artigoCortina)
    {
        $this->artigoCortina = $artigoCortina;
    }
}