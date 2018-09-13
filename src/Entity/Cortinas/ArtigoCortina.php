<?php

namespace App\Entity\Cortinas;

use App\Entity\Base\EntityId;
use App\Entity\Estoque\Produto;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Cortinas\ArtigoCortinaRepository")
 * @ORM\Table(name="crtn_artigo_cortina")
 */
class ArtigoCortina extends EntityId
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
     * @ORM\Column(name="tipo_artigo_cortina", type="string", nullable=false, length=100)
     * @Assert\NotBlank(message="O campo 'tipo_artigo_cortina' deve ser informado")
     */
    private $tipoArtigoCortina;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto")
     * @ORM\JoinColumn(name="produto_id", nullable=false)
     *
     * @var $produto Produto
     */
    private $produto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Cortinas\Tecido")
     * @ORM\JoinColumn(name="tecido_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Tecido' deve ser informado")
     *
     * @var $tecido Tecido
     */
    private $tecido;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
    }
}