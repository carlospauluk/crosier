<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktFornecedor
 *
 * @ORM\Table(name="ekt_fornecedor", indexes={@ORM\Index(name="FKfh1vhc0vjv52itc50p8egml48", columns={"estabelecimento_id"}), @ORM\Index(name="FKb0653a7sqh6opcrqncd9hrj9l", columns={"user_updated_id"}), @ORM\Index(name="FKsai078dss635rvbvacstm651w", columns={"user_inserted_id"})})
 * @ORM\Entity
 */
class EktFornecedor
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
     * @ORM\Column(name="BAIRRO", type="string", length=20, nullable=true)
     */
    private $bairro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CEP", type="string", length=9, nullable=true)
     */
    private $cep;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CGC", type="string", length=20, nullable=true)
     */
    private $cgc;

    /**
     * @var float|null
     *
     * @ORM\Column(name="CODIGO", type="float", precision=10, scale=0, nullable=true)
     */
    private $codigo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="COMPRAS_AC", type="float", precision=10, scale=0, nullable=true)
     */
    private $comprasAc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CONTATO", type="string", length=20, nullable=true)
     */
    private $contato;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATA_CAD", type="datetime", nullable=true)
     */
    private $dataCad;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATA_ULT_COMP", type="datetime", nullable=true)
     */
    private $dataUltComp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DDD_FAX", type="string", length=4, nullable=true)
     */
    private $dddFax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DDD_FONE", type="string", length=4, nullable=true)
     */
    private $dddFone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DDD_REPRES", type="string", length=4, nullable=true)
     */
    private $dddRepres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ENDERECO", type="string", length=40, nullable=true)
     */
    private $endereco;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FAX", type="string", length=9, nullable=true)
     */
    private $fax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FONE", type="string", length=9, nullable=true)
     */
    private $fone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FONE_REPRES", type="string", length=9, nullable=true)
     */
    private $foneRepres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="INSC", type="string", length=20, nullable=true)
     */
    private $insc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MUNICIPIO", type="string", length=20, nullable=true)
     */
    private $municipio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOME_FANTASIA", type="string", length=20, nullable=true)
     */
    private $nomeFantasia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOME_REPRES", type="string", length=30, nullable=true)
     */
    private $nomeRepres;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PECAS_AC", type="float", precision=10, scale=0, nullable=true)
     */
    private $pecasAc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RAZAO", type="string", length=35, nullable=true)
     */
    private $razao;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIPO", type="string", length=1, nullable=true)
     */
    private $tipo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="UF", type="string", length=2, nullable=true)
     */
    private $uf;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DT_ULTALT", type="datetime", nullable=true)
     */
    private $dtUltalt;

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
