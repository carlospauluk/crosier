<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Entidade 'Cadeia de Movimentações'.
 *
 * Movimentações podem ser dependentes umas das outras, formando uma cadeia de entradas e saídas entre carteiras.
 *
 * @ORM\Entity()
 * @ORM\Table(name="fin_cadeia")
 *
 * @author Carlos Eduardo Pauluk
 */
class Cadeia extends EntityId
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * md5(uniqid(rand(), true))
     * @ORM\Column(name="unqc", type="string", nullable=true, length=32)
     */
    private $unqc;

    /**
     *
     * Se for vinculante, ao deletar uma movimentação da cadeia todas deverão são deletadas (ver trigger trg_ad_delete_cadeia).
     *
     * @ORM\Column(name="vinculante", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $vinculante = false;

    /**
     *
     * Se for fechada, não é possível incluir outras movimentações na cadeia.
     *
     * @ORM\Column(name="fechada", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $fechada = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUnqc()
    {
        return $this->unqc;
    }

    /**
     * @param mixed $unqc
     */
    public function setUnqc($unqc): void
    {
        $this->unqc = $unqc;
    }

    public function getVinculante()
    {
        return $this->vinculante;
    }

    public function setVinculante($vinculante)
    {
        $this->vinculante = $vinculante;
    }

    public function getFechada()
    {
        return $this->fechada;
    }

    public function setFechada($fechada)
    {
        $this->fechada = $fechada;
    }
}

