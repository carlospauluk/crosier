<?php
namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Base\Endereco;
use Doctrine\Common\Collections\Collection;
use App\Entity\Base\Pessoa;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\FornecedorRepository")
 * @ORM\Table(name="est_fornecedor")
 * @ORM\HasLifecycleCallbacks()
 */
class Fornecedor extends EntityId
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
     * @Assert\NotBlank(message="O campo 'codigo' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="contato", type="string", nullable=true, length=100)
     */
    private $contato;

    /**
     *
     * @ORM\Column(name="email", type="string", nullable=true, length=50)
     */
    private $email;

    /**
     *
     * @ORM\Column(name="fone1", type="string", nullable=true, length=15)
     */
    private $fone1;

    /**
     *
     * @ORM\Column(name="fone2", type="string", nullable=true, length=15)
     */
    private $fone2;

    /**
     *
     * @ORM\Column(name="fone3", type="string", nullable=true, length=15)
     */
    private $fone3;

    /**
     *
     * @ORM\Column(name="fone4", type="string", nullable=true, length=15)
     */
    private $fone4;

    /**
     *
     * @ORM\Column(name="inscricao_estadual", type="string", nullable=true, length=20)
     */
    private $inscricaoEstadual;

    /**
     *
     * @ORM\Column(name="inscricao_municipal", type="string", nullable=true, length=40)
     */
    private $inscricaoMunicipal;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\Column(name="representante", type="string", nullable=true, length=100)
     */
    private $representante;

    /**
     *
     * @ORM\Column(name="representante_contato", type="string", nullable=true, length=100)
     */
    private $representanteContato;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Pessoa", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="pessoa_id", nullable=false)
     *
     * @var $pessoa Pessoa
     */
    private $pessoa;

    /**
     *
     * @ORM\Column(name="dt_emissao_rg", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_emissao_rg' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_emissao_rg' deve ser do tipo data/hora")
     */
    private $dtEmissaoRg;

    /**
     *
     * @ORM\Column(name="dt_nascimento", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_nascimento' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_nascimento' deve ser do tipo data/hora")
     */
    private $dtNascimento;

    /**
     *
     * @ORM\Column(name="estado_civil", type="string", nullable=true, length=13)
     */
    private $estadoCivil;

    /**
     *
     * @ORM\Column(name="estado_rg", type="string", nullable=true, length=2)
     */
    private $estadoRg;

    /**
     *
     * @ORM\Column(name="naturalidade", type="string", nullable=true, length=50)
     */
    private $naturalidade;

    /**
     *
     * @ORM\Column(name="orgao_emissor_rg", type="string", nullable=true, length=15)
     */
    private $orgaoEmissorRg;

    /**
     *
     * @ORM\Column(name="rg", type="string", nullable=true, length=15)
     */
    private $rg;

    /**
     *
     * @ORM\Column(name="sexo", type="string", nullable=true, length=9)
     */
    private $sexo;

    /**
     *
     * @ORM\Column(name="website", type="string", nullable=true, length=120)
     */
    private $website;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\FornecedorTipo")
     * @ORM\JoinColumn(name="fornecedor_tipo_id", nullable=false)
     *
     * @var $tipo FornecedorTipo
     */
    private $tipo;

    /**
     *
     * @ORM\Column(name="codigo_ekt", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $codigoEkt;

    /**
     *
     * @ORM\Column(name="codigo_ekt_desde", type="date", nullable=true)
     * @Assert\NotNull(message="O campo 'codigo_ekt_desde' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'codigo_ekt_desde' deve ser do tipo data/hora")
     */
    private $codigoEktDesde;

    /**
     *
     * @ORM\Column(name="codigo_ekt_ate", type="date", nullable=true)
     * @Assert\NotNull(message="O campo 'codigo_ekt_ate' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'codigo_ekt_ate' deve ser do tipo data/hora")
     */
    private $codigoEktAte;

    /**
     *
     * @ManyToMany(targetEntity="App\Entity\Base\Endereco", cascade={"persist"})
     * @JoinTable(name="est_fornecedor_enderecos",
     *      joinColumns={@JoinColumn(name="est_fornecedor_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="bon_endereco_id", referencedColumnName="id")}
     *      )
     */
    private $enderecos;

    public function __construct()
    {
        $this->enderecos = new ArrayCollection();
        ORM\Annotation::class;
        Assert\All::class;
        ManyToMany::class;
        JoinTable::class;
        JoinColumn::class;
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

    public function getContato()
    {
        return $this->contato;
    }

    public function setContato($contato)
    {
        $this->contato = $contato;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getFone1()
    {
        return $this->fone1;
    }

    public function setFone1($fone1)
    {
        $this->fone1 = $fone1;
    }

    public function getFone2()
    {
        return $this->fone2;
    }

    public function setFone2($fone2)
    {
        $this->fone2 = $fone2;
    }

    public function getFone3()
    {
        return $this->fone3;
    }

    public function setFone3($fone3)
    {
        $this->fone3 = $fone3;
    }

    public function getFone4()
    {
        return $this->fone4;
    }

    public function setFone4($fone4)
    {
        $this->fone4 = $fone4;
    }

    public function getInscricaoEstadual()
    {
        return $this->inscricaoEstadual;
    }

    public function setInscricaoEstadual($inscricaoEstadual)
    {
        $this->inscricaoEstadual = $inscricaoEstadual;
    }

    public function getInscricaoMunicipal()
    {
        return $this->inscricaoMunicipal;
    }

    public function setInscricaoMunicipal($inscricaoMunicipal)
    {
        $this->inscricaoMunicipal = $inscricaoMunicipal;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getRepresentante()
    {
        return $this->representante;
    }

    public function setRepresentante($representante)
    {
        $this->representante = $representante;
    }

    public function getRepresentanteContato()
    {
        return $this->representanteContato;
    }

    public function setRepresentanteContato($representanteContato)
    {
        $this->representanteContato = $representanteContato;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(?Pessoa $pessoa)
    {
        $this->pessoa = $pessoa;
    }

    public function getDtEmissaoRg()
    {
        return $this->dtEmissaoRg;
    }

    public function setDtEmissaoRg($dtEmissaoRg)
    {
        $this->dtEmissaoRg = $dtEmissaoRg;
    }

    public function getDtNascimento()
    {
        return $this->dtNascimento;
    }

    public function setDtNascimento($dtNascimento)
    {
        $this->dtNascimento = $dtNascimento;
    }

    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
    }

    public function getEstadoRg()
    {
        return $this->estadoRg;
    }

    public function setEstadoRg($estadoRg)
    {
        $this->estadoRg = $estadoRg;
    }

    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
    }

    public function getOrgaoEmissorRg()
    {
        return $this->orgaoEmissorRg;
    }

    public function setOrgaoEmissorRg($orgaoEmissorRg)
    {
        $this->orgaoEmissorRg = $orgaoEmissorRg;
    }

    public function getRg()
    {
        return $this->rg;
    }

    public function setRg($rg)
    {
        $this->rg = $rg;
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function setWebsite($website)
    {
        $this->website = $website;
    }

    public function getTipo():? FornecedorTipo
    {
        return $this->tipo;
    }

    public function setTipo(?FornecedorTipo $tipo)
    {
        $this->tipo = $tipo;
    }

    public function getCodigoEkt()
    {
        return $this->codigoEkt;
    }

    public function setCodigoEkt($codigoEkt)
    {
        $this->codigoEkt = $codigoEkt;
    }

    public function getCodigoEktDesde()
    {
        return $this->codigoEktDesde;
    }

    public function setCodigoEktDesde($codigoEktDesde)
    {
        $this->codigoEktDesde = $codigoEktDesde;
    }

    public function getCodigoEktAte()
    {
        return $this->codigoEktAte;
    }

    public function setCodigoEktAte($codigoEktAte)
    {
        $this->codigoEktAte = $codigoEktAte;
    }

    /**
     *
     * @return Collection|Endereco[]
     */
    public function getEnderecos(): Collection
    {
        return $this->enderecos;
    }

    public function addEndereco(?Endereco $e): void
    {
        if (! $this->enderecos->contains($e)) {
            $this->enderecos->add($e);
        }
    }
}