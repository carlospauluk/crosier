<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktPlanoPagto
 *
 * @ORM\Table(name="ekt_plano_pagto", indexes={@ORM\Index(name="FKcwjxy9iv96i2k9mld2r6tcqvd", columns={"estabelecimento_id"}), @ORM\Index(name="FKijb578gmygt8s6jfvbk4hx363", columns={"user_updated_id"}), @ORM\Index(name="FK491lurukx21647tj2c107b3xp", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktPlanoPagto
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
     * @var string|null
     *
     * @ORM\Column(name="COD_CREDIARIO", type="string", length=5, nullable=true)
     */
    private $codCrediario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO", type="string", length=4, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=30, nullable=true)
     */
    private $descricao;

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
     *   @ORM\JoinColumn(name="user_updated_id", referencedColumnName="id")
     * })
     */
    private $userUpdated;


}
