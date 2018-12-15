<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktVendaItem
 *
 * @ORM\Table(name="ekt_venda_item", indexes={@ORM\Index(name="FKt76l74oqgvv39fu0kvfqetaj9", columns={"estabelecimento_id"}), @ORM\Index(name="FKwl263a7mqadtnsirt4gdisry", columns={"user_updated_id"}), @ORM\Index(name="ekt_venda_item_idx2", columns={"NUMERO_NF", "RECORD_NUMBER", "PRODUTO", "mesano"}), @ORM\Index(name="FKmliu4ljc09si0cqjri0yb04oy", columns={"user_inserted_id"}), @ORM\Index(name="ekt_venda_item_idx1", columns={"mesano"})})
 * @ORM\Entity
 */
class EktVendaItem
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
     * @ORM\Column(name="DESCRICAO", type="string", length=40, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mesano", type="string", length=6, nullable=true, options={"fixed"=true})
     */
    private $mesano;

    /**
     * @var float|null
     *
     * @ORM\Column(name="NUMERO_NF", type="float", precision=10, scale=0, nullable=true)
     */
    private $numeroNf;

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
     * @ORM\Column(name="SERIE", type="string", length=1, nullable=true)
     */
    private $serie;

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
     * @ORM\Column(name="UNIDADE", type="string", length=10, nullable=true)
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
