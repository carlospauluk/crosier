<?php

namespace App\Entity\Fiscal;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Fiscal\NotaFiscalItemRepository")
 * @ORM\Table(name="fis_nf_item")
 */
class NotaFiscalItem extends EntityId
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
     * @ORM\Column(name="cfop", type="string", nullable=false, length=20)
     * @Assert\NotBlank(message="O campo 'cfop' deve ser informado")
     */
    private $cfop;

    /**
     *
     * @ORM\Column(name="codigo", type="string", nullable=false, length=50)
     * @Assert\NotBlank(message="O campo 'codigo' deve ser informado")
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=2000)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="icms", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'icms' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'icms' deve ser numérico")
     */
    private $icmsAliquota;

    /**
     *
     * @ORM\Column(name="ncm", type="string", nullable=false, length=20)
     * @Assert\NotBlank(message="O campo 'ncm' deve ser informado")
     */
    private $ncm;

    /**
     *
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'ordem' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $ordem;

    /**
     *
     * @ORM\Column(name="qtde", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'qtde' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'qtde' deve ser numérico")
     */
    private $qtde;

    /**
     *
     * @ORM\Column(name="unidade", type="string", nullable=false, length=50)
     * @Assert\NotBlank(message="O campo 'unidade' deve ser informado")
     */
    private $unidade;

    /**
     *
     * @ORM\Column(name="valor_total", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'valor_total' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'valor_total' deve ser numérico")
     */
    private $valorTotal;

    /**
     *
     * @ORM\Column(name="valor_unit", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'valor_unit' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'valor_unit' deve ser numérico")
     */
    private $valorUnit;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Fiscal\NotaFiscal")
     * @ORM\JoinColumn(name="nota_fiscal_id", nullable=false)
     *
     * @var $notaFiscal NotaFiscal
     */
    private $notaFiscal;

    /**
     *
     * @ORM\Column(name="valor_desconto", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'valor_desconto' deve ser numérico")
     */
    private $valorDesconto;

    /**
     *
     * @ORM\Column(name="sub_total", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'sub_total' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'sub_total' deve ser numérico")
     */
    private $subTotal;

    /**
     *
     * @ORM\Column(name="icms_valor", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'icms_valor' deve ser numérico")
     */
    private $icmsValor;

    /**
     *
     * @ORM\Column(name="icms_valor_bc", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'icms_valor_bc' deve ser numérico")
     */
    private $icmsValorBc;

    /**
     *
     * @ORM\Column(name="ncm_existente", type="boolean", nullable=true)
     */
    private $ncmExistente;

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

    public function getCfop()
    {
        return $this->cfop;
    }

    public function setCfop($cfop)
    {
        $this->cfop = $cfop;
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

    public function getIcmsAliquota()
    {
        return $this->icmsAliquota;
    }

    public function setIcmsAliquota($icmsAliquota)
    {
        $this->icmsAliquota = $icmsAliquota;
    }

    public function getNcm()
    {
        return $this->ncm;
    }

    public function setNcm($ncm)
    {
        $this->ncm = $ncm;
    }

    public function getOrdem()
    {
        return $this->ordem;
    }

    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    public function getQtde()
    {
        return $this->qtde;
    }

    public function setQtde($qtde)
    {
        $this->qtde = $qtde;
    }

    public function getUnidade()
    {
        return $this->unidade;
    }

    public function setUnidade($unidade)
    {
        $this->unidade = $unidade;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    public function getValorUnit()
    {
        return $this->valorUnit;
    }

    public function setValorUnit($valorUnit)
    {
        $this->valorUnit = $valorUnit;
    }

    public function getNotaFiscal()
    {
        return $this->notaFiscal;
    }

    public function setNotaFiscal($notaFiscal)
    {
        $this->notaFiscal = $notaFiscal;
    }

    public function getValorDesconto()
    {
        return $this->valorDesconto;
    }

    public function setValorDesconto($valorDesconto)
    {
        $this->valorDesconto = $valorDesconto;
    }

    public function getSubTotal()
    {
        return $this->subTotal;
    }

    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    public function getIcmsValor()
    {
        return $this->icmsValor;
    }

    public function setIcmsValor($icmsValor)
    {
        $this->icmsValor = $icmsValor;
    }

    public function getIcmsValorBc()
    {
        return $this->icmsValorBc;
    }

    public function setIcmsValorBc($icmsValorBc)
    {
        $this->icmsValorBc = $icmsValorBc;
    }

    public function getNcmExistente()
    {
        return $this->ncmExistente;
    }

    public function setNcmExistente($ncmExistente)
    {
        $this->ncmExistente = $ncmExistente;
    }

    public function calculaTotais()
    {
        if ($this->getQtde() == null || $this->getValorUnit() == null) {
            return;
        }

        $this->valorDesconto = $this->valorDesconto == null ? 0.0 : $this->valorDesconto;
        $this->subTotal = $this->getQtde() * $this->getValorUnit();
        $this->valorTotal = $this->subTotal - $this->valorDesconto;
    }
}