<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktPlanoCrediario
 *
 * @ORM\Table(name="ekt_plano_crediario", indexes={@ORM\Index(name="FKbgq2jp51svs7h5lh0uwrwn0b1", columns={"estabelecimento_id"}), @ORM\Index(name="FKi48cfxacbhiwfae6pel0ljc6x", columns={"user_updated_id"}), @ORM\Index(name="FKtelyjth3hi7qu1yuixgmlnapv", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktPlanoCrediario
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
     * @ORM\Column(name="ENTRADA", type="string", length=1, nullable=true)
     */
    private $entrada;

    /**
     * @var float|null
     *
     * @ORM\Column(name="INDICE", type="float", precision=10, scale=0, nullable=true)
     */
    private $indice;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRAZO", type="float", precision=10, scale=0, nullable=true)
     */
    private $prazo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRESTACOES", type="float", precision=10, scale=0, nullable=true)
     */
    private $prestacoes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

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
