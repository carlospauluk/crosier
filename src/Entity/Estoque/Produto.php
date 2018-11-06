<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use App\Entity\Cortinas\ArtigoCortina;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\ProdutoRepository")
 * @ORM\Table(name="est_produto")
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
     * @ORM\Column(name="reduzido", type="bigint", nullable=false)
     */
    private $reduzido;

    /**
     *
     * @ORM\Column(name="reduzido_ekt", type="integer", nullable=true)
     */
    private $reduzidoEkt;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="referencia", type="string", nullable=false, length=20)
     */
    private $referencia;

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
     * @ORM\Column(name="cst", type="string", nullable=false, length=30)
     */
    private $cst;

    /**
     *
     * @ORM\Column(name="tipo_tributacao", type="string", nullable=false, length=30)
     */
    private $tipoTributacao;

    /**
     *
     * @ORM\Column(name="icms", type="integer", nullable=false)
     */
    private $icms;

    /**
     * FIXME: isso deveria ser controlado apenas pela unidade
     * @ORM\Column(name="fracionado", type="boolean", nullable=false)
     */
    private $fracionado;

    /**
     *
     * @ORM\Column(name="ncm", type="string", nullable=false, length=30)
     */
    private $ncm;


    // ----------- CAMPOS RELACIONADOS A IMPORTAÇÃO DO EKT
    // FIXME: mais tarde poderão ser removidos


    /**
     *
     * @ORM\Column(name="reduzido_ekt_ate", type="date", nullable=true)
     * @Assert\Type("\DateTime", message="O campo 'reduzido_ekt_ate' deve ser do tipo data/hora")
     */
    private $reduzidoEktAte;

    /**
     *
     * @ORM\Column(name="reduzido_ekt_desde", type="date", nullable=true)
     * @Assert\Type("\DateTime", message="O campo 'reduzido_ekt_desde' deve ser do tipo data/hora")
     */
    private $reduzidoEktDesde;

    /**
     *
     * @ORM\Column(name="grade_err", type="string", nullable=true, length=200)
     */
    private $gradeErr;

    /**
     *
     * @ORM\Column(name="subdepto_err", type="string", nullable=true, length=200)
     */
    private $subdeptoErr;


    /**
     *
     * @ORM\Column(name="unidade_produto_err", type="string", nullable=true, length=200)
     */
    private $unidadeProdutoErr;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Depto")
     * @ORM\JoinColumn(name="depto_imp_id", nullable=true)
     *
     * @var $deptoImp Depto
     */
    private $deptoImp;


    /**
     *
     * @ORM\Column(name="dt_ult_venda", type="date", nullable=true)
     * @Assert\Type("\DateTime", message="O campo 'dt_ult_venda' deve ser do tipo data/hora")
     */
    private $dtUltVenda;


    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Cortinas\ArtigoCortina")
     * @ORM\JoinColumn(name="artigo_cortina_id", nullable=true)
     *
     * FIXME: isso deveria estar em outra tabela para não sujar esta.
     *
     * @var $artigoCortina ArtigoCortina
     */
    private $artigoCortina;

    /**
     *
     * @ORM\Column(name="atual", type="boolean", nullable=true)
     */
    private $atual = false;

    /**
     *
     * @ORM\Column(name="na_loja_virtual", type="boolean", nullable=true)
     */
    private $naLojaVirtual = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getReduzido()
    {
        return $this->reduzido;
    }

    /**
     * @param mixed $reduzido
     */
    public function setReduzido($reduzido): void
    {
        $this->reduzido = $reduzido;
    }

    /**
     * @return mixed
     */
    public function getReduzidoEkt()
    {
        return $this->reduzidoEkt;
    }

    /**
     * @param mixed $reduzidoEkt
     */
    public function setReduzidoEkt($reduzidoEkt): void
    {
        $this->reduzidoEkt = $reduzidoEkt;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return mixed
     */
    public function getReferencia()
    {
        return $this->referencia;
    }

    /**
     * @param mixed $referencia
     */
    public function setReferencia($referencia): void
    {
        $this->referencia = $referencia;
    }

    /**
     * @return Fornecedor
     */
    public function getFornecedor(): ?Fornecedor
    {
        return $this->fornecedor;
    }

    /**
     * @param Fornecedor $fornecedor
     */
    public function setFornecedor(?Fornecedor $fornecedor): void
    {
        $this->fornecedor = $fornecedor;
    }

    /**
     * @return Grade
     */
    public function getGrade(): Grade
    {
        return $this->grade;
    }

    /**
     * @param Grade $grade
     */
    public function setGrade(Grade $grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return Subdepto
     */
    public function getSubdepto(): Subdepto
    {
        return $this->subdepto;
    }

    /**
     * @param Subdepto $subdepto
     */
    public function setSubdepto(Subdepto $subdepto): void
    {
        $this->subdepto = $subdepto;
    }

    /**
     * @return UnidadeProduto
     */
    public function getUnidadeProduto(): UnidadeProduto
    {
        return $this->unidadeProduto;
    }

    /**
     * @param UnidadeProduto $unidadeProduto
     */
    public function setUnidadeProduto(UnidadeProduto $unidadeProduto): void
    {
        $this->unidadeProduto = $unidadeProduto;
    }

    /**
     * @return mixed
     */
    public function getCst()
    {
        return $this->cst;
    }

    /**
     * @param mixed $cst
     */
    public function setCst($cst): void
    {
        $this->cst = $cst;
    }

    /**
     * @return mixed
     */
    public function getTipoTributacao()
    {
        return $this->tipoTributacao;
    }

    /**
     * @param mixed $tipoTributacao
     */
    public function setTipoTributacao($tipoTributacao): void
    {
        $this->tipoTributacao = $tipoTributacao;
    }

    /**
     * @return mixed
     */
    public function getIcms()
    {
        return $this->icms;
    }

    /**
     * @param mixed $icms
     */
    public function setIcms($icms): void
    {
        $this->icms = $icms;
    }

    /**
     * @return mixed
     */
    public function getFracionado()
    {
        return $this->fracionado;
    }

    /**
     * @param mixed $fracionado
     */
    public function setFracionado($fracionado): void
    {
        $this->fracionado = $fracionado;
    }

    /**
     * @return mixed
     */
    public function getNcm()
    {
        return $this->ncm;
    }

    /**
     * @param mixed $ncm
     */
    public function setNcm($ncm): void
    {
        $this->ncm = $ncm;
    }

    /**
     * @return mixed
     */
    public function getReduzidoEktAte()
    {
        return $this->reduzidoEktAte;
    }

    /**
     * @param mixed $reduzidoEktAte
     */
    public function setReduzidoEktAte($reduzidoEktAte): void
    {
        $this->reduzidoEktAte = $reduzidoEktAte;
    }

    /**
     * @return mixed
     */
    public function getReduzidoEktDesde()
    {
        return $this->reduzidoEktDesde;
    }

    /**
     * @param mixed $reduzidoEktDesde
     */
    public function setReduzidoEktDesde($reduzidoEktDesde): void
    {
        $this->reduzidoEktDesde = $reduzidoEktDesde;
    }

    /**
     * @return mixed
     */
    public function getGradeErr()
    {
        return $this->gradeErr;
    }

    /**
     * @param mixed $gradeErr
     */
    public function setGradeErr($gradeErr): void
    {
        $this->gradeErr = $gradeErr;
    }

    /**
     * @return mixed
     */
    public function getSubdeptoErr()
    {
        return $this->subdeptoErr;
    }

    /**
     * @param mixed $subdeptoErr
     */
    public function setSubdeptoErr($subdeptoErr): void
    {
        $this->subdeptoErr = $subdeptoErr;
    }

    /**
     * @return mixed
     */
    public function getUnidadeProdutoErr()
    {
        return $this->unidadeProdutoErr;
    }

    /**
     * @param mixed $unidadeProdutoErr
     */
    public function setUnidadeProdutoErr($unidadeProdutoErr): void
    {
        $this->unidadeProdutoErr = $unidadeProdutoErr;
    }

    /**
     * @return Depto
     */
    public function getDeptoImp(): Depto
    {
        return $this->deptoImp;
    }

    /**
     * @param Depto $deptoImp
     */
    public function setDeptoImp(Depto $deptoImp): void
    {
        $this->deptoImp = $deptoImp;
    }

    /**
     * @return mixed
     */
    public function getDtUltVenda()
    {
        return $this->dtUltVenda;
    }

    /**
     * @param mixed $dtUltVenda
     */
    public function setDtUltVenda($dtUltVenda): void
    {
        $this->dtUltVenda = $dtUltVenda;
    }

    /**
     * @return mixed
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * @param mixed $obs
     */
    public function setObs($obs): void
    {
        $this->obs = $obs;
    }

    /**
     * @return ArtigoCortina
     */
    public function getArtigoCortina(): ArtigoCortina
    {
        return $this->artigoCortina;
    }

    /**
     * @param ArtigoCortina $artigoCortina
     */
    public function setArtigoCortina(ArtigoCortina $artigoCortina): void
    {
        $this->artigoCortina = $artigoCortina;
    }

    /**
     * @return mixed
     */
    public function getAtual()
    {
        return $this->atual;
    }

    /**
     * @param mixed $atual
     */
    public function setAtual($atual): void
    {
        $this->atual = $atual;
    }

    /**
     * @return mixed
     */
    public function getNaLojaVirtual()
    {
        return $this->naLojaVirtual;
    }

    /**
     * @param mixed $naLojaVirtual
     */
    public function setNaLojaVirtual($naLojaVirtual): void
    {
        $this->naLojaVirtual = $naLojaVirtual;
    }


}