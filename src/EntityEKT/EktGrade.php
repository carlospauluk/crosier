<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktGrade
 *
 * @ORM\Table(name="ekt_grade", indexes={@ORM\Index(name="FKhhvod1koyekgsi8lku1fx1999", columns={"estabelecimento_id"}), @ORM\Index(name="FKa39ujncpp983imr1xpubiekkp", columns={"user_updated_id"}), @ORM\Index(name="FK365uudlfctu56vs4j0lcadxea", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktGrade
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
     * @var string|null
     *
     * @ORM\Column(name="DEC_", type="string", length=12, nullable=true)
     */
    private $dec;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA01", type="string", length=12, nullable=true)
     */
    private $gra01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA02", type="string", length=12, nullable=true)
     */
    private $gra02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA03", type="string", length=12, nullable=true)
     */
    private $gra03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA04", type="string", length=12, nullable=true)
     */
    private $gra04;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA05", type="string", length=12, nullable=true)
     */
    private $gra05;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA06", type="string", length=12, nullable=true)
     */
    private $gra06;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA07", type="string", length=12, nullable=true)
     */
    private $gra07;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA08", type="string", length=12, nullable=true)
     */
    private $gra08;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA09", type="string", length=12, nullable=true)
     */
    private $gra09;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA10", type="string", length=12, nullable=true)
     */
    private $gra10;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA11", type="string", length=12, nullable=true)
     */
    private $gra11;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA12", type="string", length=12, nullable=true)
     */
    private $gra12;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA13", type="string", length=12, nullable=true)
     */
    private $gra13;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA14", type="string", length=12, nullable=true)
     */
    private $gra14;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GRA15", type="string", length=12, nullable=true)
     */
    private $gra15;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var \SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     * })
     */
    private $userInserted;

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


}
