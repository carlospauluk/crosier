<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktSubdepto
 *
 * @ORM\Table(name="ekt_subdepto", indexes={@ORM\Index(name="FKf44okwrt41pe9ce3lawqsh5bf", columns={"estabelecimento_id"}), @ORM\Index(name="FK78m51e0i4fbealhho1a2pd893", columns={"user_updated_id"}), @ORM\Index(name="FKfjgye4n91ivv31we8bi00p9u7", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktSubdepto
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
     * @ORM\Column(name="PECAS_AC01", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc01;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC02", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc02;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC03", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc03;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC04", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc04;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC05", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc05;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC06", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc06;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC07", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc07;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC08", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc08;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC09", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc09;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC10", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc10;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC11", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc11;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC12", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc12;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SAZON", type="string", length=1, nullable=true)
     */
    private $sazon;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC01", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc01;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC02", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc02;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC03", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc03;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC04", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc04;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC05", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc05;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC06", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc06;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC07", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc07;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC08", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc08;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC09", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc09;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC10", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc10;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC11", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc11;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDAS_AC12", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendasAc12;

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
