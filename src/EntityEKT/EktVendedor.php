<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktVendedor
 *
 * @ORM\Table(name="ekt_vendedor", indexes={@ORM\Index(name="FK5wxq5st13yaw7buo8jx4uhb18", columns={"estabelecimento_id"}), @ORM\Index(name="FK1fafm9wsf87xhsnfd7mvewhpj", columns={"user_updated_id"}), @ORM\Index(name="FKjmeh5c4etwgd8l1h4mm4310b5", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktVendedor
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
     * @var \DateTime
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     */
    private $inserted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var float|null
     *
     * @ORM\Column(name="CODIGO", type="float", precision=10, scale=0, nullable=true)
     */
    private $codigo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="COMIS_PRA", type="float", precision=10, scale=0, nullable=true)
     */
    private $comisPra;

    /**
     * @var float|null
     *
     * @ORM\Column(name="COMIS_VIS", type="float", precision=10, scale=0, nullable=true)
     */
    private $comisVis;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FLAG_GER", type="string", length=255, nullable=true)
     */
    private $flagGer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=25, nullable=true)
     */
    private $descricao;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SENHA", type="string", length=5, nullable=true)
     */
    private $senha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mesano", type="string", length=6, nullable=true, options={"fixed"=true})
     */
    private $mesano;

    /**
     * @var \SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_updated_id", referencedColumnName="id")
     * })
     */
    private $userUpdated;

    /**
     * @var \CfgEstabelecimento
     *
     * @ORM\ManyToOne(targetEntity="CfgEstabelecimento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estabelecimento_id", referencedColumnName="id")
     * })
     */
    private $estabelecimento;

    /**
     * @var \SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     * })
     */
    private $userInserted;


}
