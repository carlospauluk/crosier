<?php
namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\UnidadeProdutoRepository")
 * @ORM\Table(name="est_unidade_produto")
 */
class UnidadeProduto extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=100)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="fator", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'fator' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $fator;

    /**
     *
     * @ORM\Column(name="label", type="string", nullable=false, length=5)
     * @Assert\NotBlank(message="O campo 'label' deve ser informado")
     */
    private $label;

    /**
     *
     * @ORM\Column(name="casas_decimais", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'casas_decimais' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $casasDecimais;

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

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getFator()
    {
        return $this->fator;
    }

    public function setFator($fator)
    {
        $this->fator = $fator;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getCasasDecimais()
    {
        return $this->casasDecimais;
    }

    public function setCasasDecimais($casasDecimais)
    {
        $this->casasDecimais = $casasDecimais;
    }
}