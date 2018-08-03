<?php
namespace App\Entity\RH;

use App\Entity\Base\Endereco;
use App\Entity\Base\EntityId;
use App\Entity\Base\Pessoa;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\RH\FuncionarioRepository")
 * @ORM\Table(name="rh_funcionario")
 */
class Funcionario extends EntityId
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
     * @ORM\Column(name="clt", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'clt' deve ser informado")
     */
    private $clt;

    /**
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'codigo' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="dt_nascimento", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_nascimento' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_nascimento' deve ser do tipo data/hora")
     */
    private $dtNascimento;

    /**
     *
     * @ORM\Column(name="email", type="string", nullable=true, length=50)
     */
    private $email;

    /**
     *
     * @ORM\Column(name="estado_civil", type="string", nullable=true, length=13)
     */
    private $estadoCivil;

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
     * @ORM\Column(name="naturalidade", type="string", nullable=true, length=50)
     */
    private $naturalidade;

    /**
     *
     * @ORM\Column(name="nome_ekt", type="string", nullable=true, length=200)
     */
    private $nomeEkt;

    /**
     *
     * @ORM\Column(name="rg", type="string", nullable=true, length=15)
     */
    private $rg;

    /**
     *
     * @ORM\Column(name="senha", type="string", nullable=true, length=200)
     */
    private $senha;

    /**
     *
     * @ORM\Column(name="vendedor_comissionado", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'vendedor_comissionado' deve ser informado")
     */
    private $vendedorComissionado;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Pessoa")
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
     * @ORM\Column(name="estado_rg", type="string", nullable=true, length=2)
     */
    private $estadoRg;

    /**
     *
     * @ORM\Column(name="orgao_emissor_rg", type="string", nullable=true, length=15)
     */
    private $orgaoEmissorRg;

    /**
     *
     * @ORM\Column(name="sexo", type="string", nullable=true, length=9)
     */
    private $sexo;

    /**
     *
     * @ManyToMany(targetEntity="App\Entity\Base\Endereco")
     * @JoinTable(name="rh_funcionario_enderecos",
     *      joinColumns={@JoinColumn(name="rh_funcionario_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="bon_endereco_id", referencedColumnName="id")}
     *      )
     */
    private $enderecos;

    /**
     *
     * @ORM\Column(name="dt_admissao", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_admissao' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_admissao' deve ser do tipo data/hora")
     */
    private $dtAdmissao;

    /**
     *
     * @ORM\Column(name="dt_demissao", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_demissao' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_demissao' deve ser do tipo data/hora")
     */
    private $dtDemissao;

    /**
     *
     * @var FuncionarioCargo[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="FuncionarioCargo",
     *      mappedBy="grade",
     *      orphanRemoval=true
     * )
     */
    private $cargos;

    public function __construct()
    {
        $this->enderecos = new ArrayCollection();
        ORM\Annotation::class;
        Assert\All::class;
        ManyToMany::class;
        JoinTable::class;
        JoinColumn::class;
        $this->cargos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getClt()
    {
        return $this->clt;
    }

    public function setClt($clt)
    {
        $this->clt = $clt;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getDtNascimento()
    {
        return $this->dtNascimento;
    }

    public function setDtNascimento($dtNascimento)
    {
        $this->dtNascimento = $dtNascimento;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
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

    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
    }

    public function getNomeEkt()
    {
        return $this->nomeEkt;
    }

    public function setNomeEkt($nomeEkt)
    {
        $this->nomeEkt = $nomeEkt;
    }

    public function getRg()
    {
        return $this->rg;
    }

    public function setRg($rg)
    {
        $this->rg = $rg;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getVendedorComissionado()
    {
        return $this->vendedorComissionado;
    }

    public function setVendedorComissionado($vendedorComissionado)
    {
        $this->vendedorComissionado = $vendedorComissionado;
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

    public function getEstadoRg()
    {
        return $this->estadoRg;
    }

    public function setEstadoRg($estadoRg)
    {
        $this->estadoRg = $estadoRg;
    }

    public function getOrgaoEmissorRg()
    {
        return $this->orgaoEmissorRg;
    }

    public function setOrgaoEmissorRg($orgaoEmissorRg)
    {
        $this->orgaoEmissorRg = $orgaoEmissorRg;
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
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

    public function getDtAdmissao()
    {
        return $this->dtAdmissao;
    }

    public function setDtAdmissao($dtAdmissao)
    {
        $this->dtAdmissao = $dtAdmissao;
    }

    public function getDtDemissao()
    {
        return $this->dtDemissao;
    }

    public function setDtDemissao($dtDemissao)
    {
        $this->dtDemissao = $dtDemissao;
    }

    /**
     *
     * @return Collection|Cargo[]
     */
    public function getCargos(): Collection
    {
        return $this->cargos;
    }
}