<?php
namespace App\Entity\RH;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\RH\FuncionarioCargoRepository")
 * @ORM\Table(name="rh_funcionario_cargo")
 * @ORM\HasLifecycleCallbacks()
 */
class FuncionarioCargo extends EntityId
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
     * @ORM\Column(name="atual", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'atual' deve ser informado")
     */
    private $atual;

    /**
     *
     * @ORM\Column(name="comissao", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'comissao' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'comissao' deve ser numérico")
     */
    private $comissao;

    /**
     *
     * @ORM\Column(name="dt_fim", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_fim' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_fim' deve ser do tipo data/hora")
     */
    private $dtFim;

    /**
     *
     * @ORM\Column(name="dt_inicio", type="datetime", nullable=false)
     * @Assert\Type("\DateTime", message="O campo 'dt_inicio' deve ser do tipo data/hora")
     */
    private $dtInicio;

    /**
     *
     * @ORM\Column(name="salario", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'salario' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'salario' deve ser numérico")
     */
    private $salario;

    /**
     *
     * @ORM\Column(name="salario_piso", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'salario_piso' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'salario_piso' deve ser numérico")
     */
    private $salarioPiso;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\RH\Cargo")
     * @ORM\JoinColumn(name="cargo_id", nullable=false)
     *
     * @var $cargo Cargo
     */
    private $cargo;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\RH\Funcionario")
     * @ORM\JoinColumn(name="funcionario_id", nullable=false)
     *
     * @var $funcionario Funcionario
     */
    private $funcionario;

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

    public function getAtual()
    {
        return $this->atual;
    }

    public function setAtual($atual)
    {
        $this->atual = $atual;
    }

    public function getComissao()
    {
        return $this->comissao;
    }

    public function setComissao($comissao)
    {
        $this->comissao = $comissao;
    }

    public function getDtFim()
    {
        return $this->dtFim;
    }

    public function setDtFim($dtFim)
    {
        $this->dtFim = $dtFim;
    }

    public function getDtInicio()
    {
        return $this->dtInicio;
    }

    public function setDtInicio($dtInicio)
    {
        $this->dtInicio = $dtInicio;
    }

    public function getSalario()
    {
        return $this->salario;
    }

    public function setSalario($salario)
    {
        $this->salario = $salario;
    }

    public function getSalarioPiso()
    {
        return $this->salarioPiso;
    }

    public function setSalarioPiso($salarioPiso)
    {
        $this->salarioPiso = $salarioPiso;
    }

    public function getCargo(): ?Cargo
    {
        return $this->cargo;
    }

    public function setCargo(?Cargo $cargo)
    {
        $this->cargo = $cargo;
    }

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario)
    {
        $this->funcionario = $funcionario;
    }
}