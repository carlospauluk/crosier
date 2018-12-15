<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktDepto
 *
 * @ORM\Table(name="ekt_depto", indexes={@ORM\Index(name="FKh9hkdfpsni0cqugbm7qjebnbk", columns={"estabelecimento_id"}), @ORM\Index(name="FKmcpjg4qugvasjmf1973fwa5xq", columns={"user_updated_id"}), @ORM\Index(name="FKf9opljoudkvyqtvd3wc1q989i", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktDepto
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
     * @ORM\Column(name="DESCRICAO", type="string", length=25, nullable=true)
     */
    private $descricao;

    /**
     * @var float|null
     *
     * @ORM\Column(name="MARGEM", type="float", precision=10, scale=0, nullable=true)
     */
    private $margem;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc;

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
