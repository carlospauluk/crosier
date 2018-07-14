<?php
namespace App\Entity\Cortinas;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Cortinas\TecidoRepository")
 * @ORM\Table(name="crtn_tecido")
 */
class Tecido extends EntityId
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
     * @ORM\Column(name="altura_barra_padrao", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'altura_barra_padrao' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'altura_barra_padrao' deve ser numérico")
     */
    private $alturaBarraPadrao;

    /**
     *
     * @ORM\Column(name="altura_max_horizontal", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'altura_max_horizontal' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'altura_max_horizontal' deve ser numérico")
     */
    private $alturaMaxHorizontal;

    /**
     *
     * @ORM\Column(name="fator_padrao", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $fatorPadrao;

    /**
     *
     * @ORM\Column(name="largura", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'largura' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'largura' deve ser numérico")
     */
    private $largura;

    /**
     *
     * @ORM\Column(name="orientacao_padrao", type="string", nullable=true, length=30)
     */
    private $orientacaoPadrao;

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

    public function getAlturaBarraPadrao()
    {
        return $this->alturaBarraPadrao;
    }

    public function setAlturaBarraPadrao($alturaBarraPadrao)
    {
        $this->alturaBarraPadrao = $alturaBarraPadrao;
    }

    public function getAlturaMaxHorizontal()
    {
        return $this->alturaMaxHorizontal;
    }

    public function setAlturaMaxHorizontal($alturaMaxHorizontal)
    {
        $this->alturaMaxHorizontal = $alturaMaxHorizontal;
    }

    public function getFatorPadrao()
    {
        return $this->fatorPadrao;
    }

    public function setFatorPadrao($fatorPadrao)
    {
        $this->fatorPadrao = $fatorPadrao;
    }

    public function getLargura()
    {
        return $this->largura;
    }

    public function setLargura($largura)
    {
        $this->largura = $largura;
    }

    public function getOrientacaoPadrao()
    {
        return $this->orientacaoPadrao;
    }

    public function setOrientacaoPadrao($orientacaoPadrao)
    {
        $this->orientacaoPadrao = $orientacaoPadrao;
    }
}