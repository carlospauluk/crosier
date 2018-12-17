<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktEncomendaItem
 *
 * @ORM\Table(name="ekt_encomenda_item", indexes={@ORM\Index(name="FK6k4gxmuat8b02acvcw47tgpbn", columns={"estabelecimento_id"}), @ORM\Index(name="FKh6qxbcfjeax49ir2u7eqa4h1", columns={"user_updated_id"}), @ORM\Index(name="FKkoybgrwuc50rc9ctkkvvu1bmx", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktEncomendaItem
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
     * @ORM\Column(name="DEPTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $depto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=40, nullable=true)
     */
    private $descricao;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="EMISSAO", type="datetime", nullable=true)
     */
    private $emissao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FLAG", type="string", length=1, nullable=true)
     */
    private $flag;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FLAG_INT", type="string", length=1, nullable=true)
     */
    private $flagInt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="FORNEC", type="float", precision=10, scale=0, nullable=true)
     */
    private $fornec;

    /**
     * @var float|null
     *
     * @ORM\Column(name="GRADE", type="float", precision=10, scale=0, nullable=true)
     */
    private $grade;

    /**
     * @var float|null
     *
     * @ORM\Column(name="NUMERO_NF", type="float", precision=10, scale=0, nullable=true)
     */
    private $numeroNf;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OBS", type="string", length=50, nullable=true)
     */
    private $obs;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRECO_CUSTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $precoCusto;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRECO_VISTA", type="float", precision=10, scale=0, nullable=true)
     */
    private $precoVista;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRODUTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $produto;

    /**
     * @var float|null
     *
     * @ORM\Column(name="QTDE", type="float", precision=10, scale=0, nullable=true)
     */
    private $qtde;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="REFERENCIA", type="string", length=8, nullable=true)
     */
    private $referencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SERIE", type="string", length=1, nullable=true)
     */
    private $serie;

    /**
     * @var float|null
     *
     * @ORM\Column(name="SUBDEPTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $subdepto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TAMANHO", type="string", length=3, nullable=true)
     */
    private $tamanho;

    /**
     * @var float|null
     *
     * @ORM\Column(name="TELA", type="float", precision=10, scale=0, nullable=true)
     */
    private $tela;

    /**
     * @var string|null
     *
     * @ORM\Column(name="UNIDADE", type="string", length=2, nullable=true)
     */
    private $unidade;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VLR_TOTAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $vlrTotal;

    /**
     * @var float|null
     *
     * @ORM\Column(name="VLR_UNIT", type="float", precision=10, scale=0, nullable=true)
     */
    private $vlrUnit;

    /**
     * @var float|null
     *
     * @ORM\Column(name="WIN", type="float", precision=10, scale=0, nullable=true)
     */
    private $win;

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
