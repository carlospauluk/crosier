<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktEncomenda
 *
 * @ORM\Table(name="ekt_encomenda", indexes={@ORM\Index(name="FKbkr3aj51lho5p52y8bfvgfjmu", columns={"estabelecimento_id"}), @ORM\Index(name="FK5cevkibrrxp2av948wgxb0766", columns={"user_updated_id"}), @ORM\Index(name="FKt9cd1mjxnw7s8nely5hfreh03", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktEncomenda
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
     * @ORM\Column(name="CLIENTE", type="float", precision=10, scale=0, nullable=true)
     */
    private $cliente;

    /**
     * @var float|null
     *
     * @ORM\Column(name="COD_PLANO", type="float", precision=10, scale=0, nullable=true)
     */
    private $codPlano;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COND_PAG", type="string", length=4, nullable=true)
     */
    private $condPag;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DESC_ACRES", type="float", precision=10, scale=0, nullable=true)
     */
    private $descAcres;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DESC_ESPECIAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $descEspecial;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="EMISSAO", type="datetime", nullable=true)
     */
    private $emissao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="EMITIDO", type="string", length=1, nullable=true)
     */
    private $emitido;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FLAG_DV", type="string", length=1, nullable=true)
     */
    private $flagDv;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FONE", type="string", length=11, nullable=true)
     */
    private $fone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="HIST_DESC", type="string", length=40, nullable=true)
     */
    private $histDesc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MENSAGEM", type="string", length=50, nullable=true)
     */
    private $mensagem;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOME_CLIENTE", type="string", length=15, nullable=true)
     */
    private $nomeCliente;

    /**
     * @var float|null
     *
     * @ORM\Column(name="NUMERO", type="float", precision=10, scale=0, nullable=true)
     */
    private $numero;

    /**
     * @var float|null
     *
     * @ORM\Column(name="P1", type="float", precision=10, scale=0, nullable=true)
     */
    private $p1;

    /**
     * @var float|null
     *
     * @ORM\Column(name="P2", type="float", precision=10, scale=0, nullable=true)
     */
    private $p2;

    /**
     * @var float|null
     *
     * @ORM\Column(name="P3", type="float", precision=10, scale=0, nullable=true)
     */
    private $p3;

    /**
     * @var float|null
     *
     * @ORM\Column(name="P4", type="float", precision=10, scale=0, nullable=true)
     */
    private $p4;

    /**
     * @var float|null
     *
     * @ORM\Column(name="P5", type="float", precision=10, scale=0, nullable=true)
     */
    private $p5;

    /**
     * @var float|null
     *
     * @ORM\Column(name="P6", type="float", precision=10, scale=0, nullable=true)
     */
    private $p6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PLANO", type="string", length=50, nullable=true)
     */
    private $plano;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PRAZO", type="string", length=3, nullable=true)
     */
    private $prazo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var float|null
     *
     * @ORM\Column(name="SDO_PAGAR", type="float", precision=10, scale=0, nullable=true)
     */
    private $sdoPagar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SERIE", type="string", length=1, nullable=true)
     */
    private $serie;

    /**
     * @var float|null
     *
     * @ORM\Column(name="SUB_TOTAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $subTotal;

    /**
     * @var float|null
     *
     * @ORM\Column(name="TOTAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $total;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="V1", type="datetime", nullable=true)
     */
    private $v1;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="V2", type="datetime", nullable=true)
     */
    private $v2;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="V3", type="datetime", nullable=true)
     */
    private $v3;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="V4", type="datetime", nullable=true)
     */
    private $v4;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="V5", type="datetime", nullable=true)
     */
    private $v5;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="V6", type="datetime", nullable=true)
     */
    private $v6;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VENDEDOR", type="float", precision=10, scale=0, nullable=true)
     */
    private $vendedor;

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
