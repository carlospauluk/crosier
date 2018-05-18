<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Utils\StringUtils;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\CategoriaRepository")
 * @ORM\Table(name="fin_categoria")
 */
class Categoria extends EntityId
{

    public const MASK = "0.00.000.000.0000.00000";

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Categoria")
     * @ORM\JoinColumn(nullable=true)
     */
    private $pai;

    /**
     *
     * @var Categoria[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Categoria",
     *      mappedBy="pai",
     *      orphanRemoval=true
     * )
     */
    private $subCategs;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="descricao_padrao_moviment", type="string", nullable=false, length=200)
     * @Assert\NotBlank()
     */
    private $descricaoPadraoMoviment;

    /**
     *
     * @ORM\Column(name="codigo", type="bigint", nullable=false)
     * @Assert\NotBlank()
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="totalizavel", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $totalizavel = false;

    /**
     *
     * @ORM\Column(name="centro_custo_dif", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $centroCustoDif = false;

    /**
     *
     * @ORM\Column(name="roles_acess", type="string", nullable=true, length=2000)
     */
    private $roles_acess;

    /**
     *
     * @ORM\Column(name="descricao_alternativa", type="string", nullable=true, length=200)
     * @Assert\NotBlank()
     */
    private $descricaoAlternativa;

    /**
     *
     * @ORM\Column(name="codigo_super", type="bigint", nullable=true)
     * @Assert\NotNull()
     */
    private $codigoSuper;

    /**
     * 
     */
    public function __construct()
    {
        $this->subCategs = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPai()
    {
        return $this->pai;
    }

    public function setPai($pai)
    {
        $this->pai = $pai;
    }

    /**
     * @return Collection|Categoria[]
     */
    public function getSubCategs(): Collection
    {
        return $this->subCategs;
    }

    public function setSubCategs($subCategs)
    {
        $this->subCategs = $subCategs;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDescricaoPadraoMoviment()
    {
        return $this->descricaoPadraoMoviment;
    }

    public function setDescricaoPadraoMoviment($descricaoPadraoMoviment)
    {
        $this->descricaoPadraoMoviment = $descricaoPadraoMoviment;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getTotalizavel()
    {
        return $this->totalizavel;
    }

    public function setTotalizavel($totalizavel)
    {
        $this->totalizavel = $totalizavel;
    }

    public function getCentroCustoDif()
    {
        return $this->centroCustoDif;
    }

    public function setCentroCustoDif($centroCustoDif)
    {
        $this->centroCustoDif = $centroCustoDif;
    }

    public function getRoles_acess()
    {
        return $this->roles_acess;
    }

    public function setRoles_acess($roles_acess)
    {
        $this->roles_acess = $roles_acess;
    }

    public function getDescricaoAlternativa()
    {
        return $this->descricaoAlternativa;
    }

    public function setDescricaoAlternativa($descricaoAlternativa)
    {
        $this->descricaoAlternativa = $descricaoAlternativa;
    }

    public function getCodigoSuper()
    {
        return $this->codigoSuper;
    }

    public function setCodigoSuper($codigoSuper)
    {
        $this->codigoSuper = $codigoSuper;
    }

    /**
     * Retorna a descrição de uma Categoria no formato codigo + descricao (Ex.:
     * 2.01 - DESPESAS PESSOAIS).
     *
     * @return
     */
    public function getDescricaoMontada()
    {
        return $this->getCodigo() . " - " . $this->getDescricao();
    }

    public function getCodigoM()
    {
        return StringUtils::mascarar($this->getCodigo(), Categoria::MASK);
    }

    public function getCodigoSufixo()
    {
        if ($this->getCodigo()) {
            if (! $this->getPai()) {
                return $this->getCodigo();
            } else {
                // Se tem pai, é o restante do código, removendo a parte do pai:
                return substr($this->getPai()->getCodigoM(), strlen($this->getPai()->getCodigoM()) + 1);
            }
        }
        return null;
    }
}

