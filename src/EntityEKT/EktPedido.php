<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktPedido
 *
 * @ORM\Table(name="ekt_pedido", indexes={@ORM\Index(name="FKsjwaxx5rcwpi67ap4bml9ri2x", columns={"estabelecimento_id"}), @ORM\Index(name="FK31bmbmyixo86ku8v2y85msaq6", columns={"user_updated_id"}), @ORM\Index(name="FKlw718dstlpvvtwck466drrwg6", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktPedido
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
     * @ORM\Column(name="ANO_ENT", type="float", precision=10, scale=0, nullable=true)
     */
    private $anoEnt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DD1", type="float", precision=10, scale=0, nullable=true)
     */
    private $dd1;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DD2", type="float", precision=10, scale=0, nullable=true)
     */
    private $dd2;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DD3", type="float", precision=10, scale=0, nullable=true)
     */
    private $dd3;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DD4", type="float", precision=10, scale=0, nullable=true)
     */
    private $dd4;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DD5", type="float", precision=10, scale=0, nullable=true)
     */
    private $dd5;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DESCDP", type="float", precision=10, scale=0, nullable=true)
     */
    private $descdp;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DESCNF", type="float", precision=10, scale=0, nullable=true)
     */
    private $descnf;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="EMISSAO", type="datetime", nullable=true)
     */
    private $emissao;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="ENTREGA", type="datetime", nullable=true)
     */
    private $entrega;

    /**
     * @var float|null
     *
     * @ORM\Column(name="FORNEC", type="float", precision=10, scale=0, nullable=true)
     */
    private $fornec;

    /**
     * @var float|null
     *
     * @ORM\Column(name="MES_ENT", type="float", precision=10, scale=0, nullable=true)
     */
    private $mesEnt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PTOTAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $ptotal;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PTOTALBX", type="float", precision=10, scale=0, nullable=true)
     */
    private $ptotalbx;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PUNIT", type="float", precision=10, scale=0, nullable=true)
     */
    private $punit;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PEDIDO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pedido;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRAZO", type="float", precision=10, scale=0, nullable=true)
     */
    private $prazo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="QTDE", type="float", precision=10, scale=0, nullable=true)
     */
    private $qtde;

    /**
     * @var float|null
     *
     * @ORM\Column(name="QTDEBX", type="float", precision=10, scale=0, nullable=true)
     */
    private $qtdebx;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var float|null
     *
     * @ORM\Column(name="SUB_DEPTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $subDepto;

    /**
     * @var float|null
     *
     * @ORM\Column(name="TOTAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $total;

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
