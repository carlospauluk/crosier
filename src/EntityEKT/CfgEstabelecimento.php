<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * CfgEstabelecimento
 *
 * @ORM\Table(name="cfg_estabelecimento", uniqueConstraints={@ORM\UniqueConstraint(name="UK1lwyp27f2exopwvrxb27lxi8s", columns={"codigo"})}, indexes={@ORM\Index(name="FKc11vu43495ohqqulfv61j2m7h", columns={"pai_id"})})
 * @ORM\Entity
 */
class CfgEstabelecimento
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="codigo", type="bigint", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     */
    private $descricao;

    /**
     * @var int|null
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var bool
     *
     * @ORM\Column(name="concreto", type="boolean", nullable=false)
     */
    private $concreto;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated", type="date", nullable=true)
     */
    private $updated;

    /**
     * @var \CfgEstabelecimento
     *
     * @ORM\ManyToOne(targetEntity="CfgEstabelecimento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pai_id", referencedColumnName="id")
     * })
     */
    private $pai;


}
