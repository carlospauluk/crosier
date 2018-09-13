<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\DepreciacaoPrecoRepository")
 * @ORM\Table(name="est_depreciacao_preco")
 */
class DepreciacaoPreco extends EntityId
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
     * @ORM\Column(name="prazo_fim", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'prazo_fim' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $prazoFim;

    /**
     *
     * @ORM\Column(name="prazo_ini", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'prazo_ini' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $prazoIni;

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

    public function getPrazoFim()
    {
        return $this->prazoFim;
    }

    public function setPrazoFim($prazoFim)
    {
        $this->prazoFim = $prazoFim;
    }

    public function getPrazoIni()
    {
        return $this->prazoIni;
    }

    public function setPrazoIni($prazoIni)
    {
        $this->prazoIni = $prazoIni;
    }
}