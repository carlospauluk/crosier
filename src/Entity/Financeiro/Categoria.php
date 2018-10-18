<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use App\Utils\StringUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\JoinColumn(name="pai_id",nullable=true)
     *
     * @var $pai Categoria
     */
    private $pai;

    /**
     *
     * @var Categoria[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Categoria",
     *      mappedBy="pai"
     * )
     * @Groups({"private"})
     */
    private $subCategs;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     * Para os casos onde a movimentação é importada automaticamente, define qual a descrição padrão.
     *
     * @ORM\Column(name="descricao_padrao_moviment", type="string", nullable=false, length=200)
     * @Assert\NotBlank()
     */
    private $descricaoPadraoMoviment;

    /**
     *
     * @ORM\Column(name="codigo", type="bigint", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min=1)
     */
    private $codigo;

    /**
     * A fim de relatórios.
     *
     * @ORM\Column(name="totalizavel", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $totalizavel = false;

    /**
     * Informa se esta categoria necessita que o CentroCusto seja informado (ou se ele será automático).
     *
     * @ORM\Column(name="centro_custo_dif", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $centroCustoDif = false;

    /**
     * Informa quais ROLES possuem acesso as informações (categoria.descricao e movimentacao.descricao).
     * Para mais de uma, informar separado por vírgula.
     *
     * @ORM\Column(name="roles_acess", type="string", nullable=true, length=2000)
     */
    private $rolesAcess;

    /**
     *
     * Caso o usuário logado não possua nenhuma das "rolesAcess", então a descrição alternativa deve ser exibida.
     *
     * @ORM\Column(name="descricao_alternativa", type="string", nullable=true, length=200)
     * @Assert\NotBlank()
     */
    private $descricaoAlternativa;

    /**
     * Atalho para não precisar ficar fazendo parse.
     *
     * @ORM\Column(name="codigo_super", type="bigint", nullable=true)
     * @Assert\NotNull()
     */
    private $codigoSuper;

    /**
     * Atalho para não precisar ficar fazendo parse.
     *
     * @ORM\Column(name="codigo_ord", type="bigint", nullable=true)
     * @Assert\NotNull()
     */
    private $codigoOrd;

    /**
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

    public function getPai(): ?Categoria
    {
        return $this->pai;
    }

    public function setPai(?Categoria $pai)
    {
        $this->pai = $pai;
    }

    /**
     *
     * @return Collection|Categoria[]
     */
    public function getSubCategs(): Collection
    {
        return $this->subCategs;
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

    public function getRolesAcess()
    {
        return $this->rolesAcess;
    }

    public function setRoles_acess($rolesAcess)
    {
        $this->roles_acess = $rolesAcess;
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

    public function getCodigoOrd()
    {
        return $this->codigoOrd;
    }

    public function setCodigoOrd($codigoOrd)
    {
        $this->codigoOrd = $codigoOrd;
    }

    /**
     * Retorna a descrição de uma Categoria no formato codigo + descricao (Ex.:
     * 2.01 - DESPESAS PESSOAIS).
     *
     * @return
     */
    public function getDescricaoMontada()
    {
        return $this->getCodigoM() . " - " . $this->getDescricao();
    }

    /**
     * Retorna a descrição de uma Categoria no formato codigo + descricao (Ex.:
     * 2.01 - DESPESAS PESSOAIS).
     *
     * @return
     */
    public function getDescricaoMontadaTree()
    {
        return str_pad('', strlen($this->getCodigo())-1, '.') . " " . $this->getCodigoM() . " - " . $this->getDescricao();
    }

    public function getCodigoM()
    {
        return StringUtils::mascarar($this->getCodigo(), Categoria::MASK);
    }

    /**
     * Retorna somente o último 'bloco' do código.
     */
    public function getCodigoSufixo()
    {
        if ($this->getCodigo()) {
            if (!$this->getPai()) {
                return $this->getCodigo();
            } else {
                // Se tem pai, é o restante do código, removendo a parte do pai:
                return substr($this->getPai()->getCodigoM(), strlen($this->getPai()->getCodigoM()) + 1);
            }
        }
        return null;
    }
}

