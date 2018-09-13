<?php

namespace App\Entity\CRM;

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
 * @ORM\Entity(repositoryClass="App\Repository\CRM\ClienteRepository")
 * @ORM\Table(name="crm_cliente")
 */
class Cliente extends EntityId
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
     * @ORM\Column(name="estado_rg", type="string", nullable=true, length=2)
     */
    private $estadoRg;

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
     * @ORM\Column(name="naturalidade", type="string", nullable=true, length=50)
     */
    private $naturalidade;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Pessoa", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="pessoa_id", nullable=false)
     *
     */
    private $pessoa;

    /**
     *
     * @ORM\Column(name="aceita_whatsapp", type="boolean", nullable=true)
     */
    private $aceitaWhatsapp;

    /**
     *
     * @ORM\Column(name="tem_whatsapp", type="boolean", nullable=true)
     */
    private $temWhatsapp;

    /**
     *
     * @ManyToMany(targetEntity="App\Entity\Base\Endereco",cascade={"persist"})
     * @JoinTable(name="crm_cliente_enderecos",
     *      joinColumns={@JoinColumn(name="crm_cliente_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="bon_endereco_id", referencedColumnName="id")})
     */
    private $enderecos;

    public function __construct()
    {
        $this->enderecos = new ArrayCollection();
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

    public function getEstadoRg()
    {
        return $this->estadoRg;
    }

    public function setEstadoRg($estadoRg)
    {
        $this->estadoRg = $estadoRg;
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

    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
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

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(?Pessoa $pessoa)
    {
        $this->pessoa = $pessoa;
    }

    public function getAceitaWhatsapp()
    {
        return $this->aceitaWhatsapp;
    }

    public function setAceitaWhatsapp($aceitaWhatsapp)
    {
        $this->aceitaWhatsapp = $aceitaWhatsapp;
    }

    public function getTemWhatsapp()
    {
        return $this->temWhatsapp;
    }

    public function setTemWhatsapp($temWhatsapp)
    {
        $this->temWhatsapp = $temWhatsapp;
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
        if (!$this->enderecos->contains($e)) {
            $this->enderecos->add($e);
        }
    }
}