<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * EktProduto
 *
 * @ORM\Table(name="ekt_produto", uniqueConstraints={@ORM\UniqueConstraint(name="ekt_produto_idx1", columns={"REDUZIDO", "mesano"})}, indexes={@ORM\Index(name="FKd9jcu670etwggd8n8lddk0ho7", columns={"estabelecimento_id"}), @ORM\Index(name="FKig5x4d9bvyi054598mjse6oqh", columns={"user_updated_id"}), @ORM\Index(name="ekt_produto_idx3", columns={"mesano"}), @ORM\Index(name="FKj04004v7chrfrvf1urwsi4onp", columns={"user_inserted_id"}), @ORM\Index(name="ekt_produto_idx2", columns={"REDUZIDO"})})
 * @ORM\Entity
 */
class EktProduto
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
     * @ORM\Column(name="AC01", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac01;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC02", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac02;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC03", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac03;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC04", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac04;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC05", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac05;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC06", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac06;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC07", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac07;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC08", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac08;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC09", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac09;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC10", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac10;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC11", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac11;

    /**
     * @var float|null
     *
     * @ORM\Column(name="AC12", type="float", precision=10, scale=0, nullable=true)
     */
    private $ac12;

    /**
     * @var float|null
     *
     * @ORM\Column(name="COEF", type="float", precision=10, scale=0, nullable=true)
     */
    private $coef;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATA_CAD", type="datetime", nullable=true)
     */
    private $dataCad;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATA_PCUSTO", type="datetime", nullable=true)
     */
    private $dataPcusto;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATA_PVENDA", type="datetime", nullable=true)
     */
    private $dataPvenda;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATA_ULT_VENDA", type="datetime", nullable=true)
     */
    private $dataUltVenda;

    /**
     * @var float|null
     *
     * @ORM\Column(name="DEPTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $depto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=35, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F1", type="string", length=1, nullable=true)
     */
    private $f1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F10", type="string", length=1, nullable=true)
     */
    private $f10;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F11", type="string", length=1, nullable=true)
     */
    private $f11;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F12", type="string", length=1, nullable=true)
     */
    private $f12;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F2", type="string", length=1, nullable=true)
     */
    private $f2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F3", type="string", length=1, nullable=true)
     */
    private $f3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F4", type="string", length=1, nullable=true)
     */
    private $f4;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F5", type="string", length=1, nullable=true)
     */
    private $f5;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F6", type="string", length=1, nullable=true)
     */
    private $f6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F7", type="string", length=1, nullable=true)
     */
    private $f7;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F8", type="string", length=1, nullable=true)
     */
    private $f8;

    /**
     * @var string|null
     *
     * @ORM\Column(name="F9", type="string", length=1, nullable=true)
     */
    private $f9;

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
     * @ORM\Column(name="MARGEM", type="float", precision=10, scale=0, nullable=true)
     */
    private $margem;

    /**
     * @var float|null
     *
     * @ORM\Column(name="MARGEMC", type="float", precision=10, scale=0, nullable=true)
     */
    private $margemc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MODELO", type="string", length=3, nullable=true)
     */
    private $modelo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="OVL_PROD", type="string", length=11, nullable=true)
     */
    private $ovlProd;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PCUSTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pcusto;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PPRAZO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pprazo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PPROMO", type="float", precision=10, scale=0, nullable=true)
     */
    private $ppromo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PVISTA", type="float", precision=10, scale=0, nullable=true)
     */
    private $pvista;

    /**
     * @var float|null
     *
     * @ORM\Column(name="PRAZO", type="float", precision=10, scale=0, nullable=true)
     */
    private $prazo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT01", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT02", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT03", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT04", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt04;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT05", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt05;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT06", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt06;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT07", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt07;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT08", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt08;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT09", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt09;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT10", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt10;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT11", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt11;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT12", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt12;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT13", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt13;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT14", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt14;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QT15", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $qt15;

    /**
     * @var float|null
     *
     * @ORM\Column(name="QTDE_MES", type="float", precision=10, scale=0, nullable=true)
     */
    private $qtdeMes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="RECORD_NUMBER", type="integer", nullable=true)
     */
    private $recordNumber;

    /**
     * @var float|null
     *
     * @ORM\Column(name="REDUZIDO", type="float", precision=10, scale=0, nullable=true)
     */
    private $reduzido;

    /**
     * @var string|null
     *
     * @ORM\Column(name="REFERENCIA", type="string", length=8, nullable=true)
     */
    private $referencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="STATUS", type="string", length=1, nullable=true)
     */
    private $status;

    /**
     * @var float|null
     *
     * @ORM\Column(name="SUBDEPTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $subdepto;

    /**
     * @var float|null
     *
     * @ORM\Column(name="ULT_VENDER", type="float", precision=10, scale=0, nullable=true)
     */
    private $ultVender;

    /**
     * @var string|null
     *
     * @ORM\Column(name="UNIDADE", type="string", length=5, nullable=true)
     */
    private $unidade;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CST", type="string", length=3, nullable=true)
     */
    private $cst;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FRACIONADO", type="string", length=1, nullable=true)
     */
    private $fracionado;

    /**
     * @var float|null
     *
     * @ORM\Column(name="ICMS", type="float", precision=10, scale=0, nullable=true)
     */
    private $icms;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NCM", type="string", length=8, nullable=true)
     */
    private $ncm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TIPO_TRIB", type="string", length=1, nullable=true)
     */
    private $tipoTrib;

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
     * @var string|null
     *
     * @ORM\Column(name="UNIDADE_CORR", type="string", length=5, nullable=true)
     */
    private $unidadeCorr;

    /**
     * @var CfgEstabelecimento
     *
     * @ORM\ManyToOne(targetEntity="CfgEstabelecimento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estabelecimento_id", referencedColumnName="id")
     * })
     */
    private $estabelecimento;

    /**
     * @var SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_updated_id", referencedColumnName="id")
     * })
     */
    private $userUpdated;

    /**
     * @var SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     * })
     */
    private $userInserted;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getInserted(): \DateTime
    {
        return $this->inserted;
    }

    /**
     * @param \DateTime $inserted
     */
    public function setInserted(\DateTime $inserted): void
    {
        $this->inserted = $inserted;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return int|null
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @param int|null $version
     */
    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }

    /**
     * @return float|null
     */
    public function getAc01(): ?float
    {
        return $this->ac01;
    }

    /**
     * @param float|null $ac01
     */
    public function setAc01(?float $ac01): void
    {
        $this->ac01 = $ac01;
    }

    /**
     * @return float|null
     */
    public function getAc02(): ?float
    {
        return $this->ac02;
    }

    /**
     * @param float|null $ac02
     */
    public function setAc02(?float $ac02): void
    {
        $this->ac02 = $ac02;
    }

    /**
     * @return float|null
     */
    public function getAc03(): ?float
    {
        return $this->ac03;
    }

    /**
     * @param float|null $ac03
     */
    public function setAc03(?float $ac03): void
    {
        $this->ac03 = $ac03;
    }

    /**
     * @return float|null
     */
    public function getAc04(): ?float
    {
        return $this->ac04;
    }

    /**
     * @param float|null $ac04
     */
    public function setAc04(?float $ac04): void
    {
        $this->ac04 = $ac04;
    }

    /**
     * @return float|null
     */
    public function getAc05(): ?float
    {
        return $this->ac05;
    }

    /**
     * @param float|null $ac05
     */
    public function setAc05(?float $ac05): void
    {
        $this->ac05 = $ac05;
    }

    /**
     * @return float|null
     */
    public function getAc06(): ?float
    {
        return $this->ac06;
    }

    /**
     * @param float|null $ac06
     */
    public function setAc06(?float $ac06): void
    {
        $this->ac06 = $ac06;
    }

    /**
     * @return float|null
     */
    public function getAc07(): ?float
    {
        return $this->ac07;
    }

    /**
     * @param float|null $ac07
     */
    public function setAc07(?float $ac07): void
    {
        $this->ac07 = $ac07;
    }

    /**
     * @return float|null
     */
    public function getAc08(): ?float
    {
        return $this->ac08;
    }

    /**
     * @param float|null $ac08
     */
    public function setAc08(?float $ac08): void
    {
        $this->ac08 = $ac08;
    }

    /**
     * @return float|null
     */
    public function getAc09(): ?float
    {
        return $this->ac09;
    }

    /**
     * @param float|null $ac09
     */
    public function setAc09(?float $ac09): void
    {
        $this->ac09 = $ac09;
    }

    /**
     * @return float|null
     */
    public function getAc10(): ?float
    {
        return $this->ac10;
    }

    /**
     * @param float|null $ac10
     */
    public function setAc10(?float $ac10): void
    {
        $this->ac10 = $ac10;
    }

    /**
     * @return float|null
     */
    public function getAc11(): ?float
    {
        return $this->ac11;
    }

    /**
     * @param float|null $ac11
     */
    public function setAc11(?float $ac11): void
    {
        $this->ac11 = $ac11;
    }

    /**
     * @return float|null
     */
    public function getAc12(): ?float
    {
        return $this->ac12;
    }

    /**
     * @param float|null $ac12
     */
    public function setAc12(?float $ac12): void
    {
        $this->ac12 = $ac12;
    }

    /**
     * @return float|null
     */
    public function getCoef(): ?float
    {
        return $this->coef;
    }

    /**
     * @param float|null $coef
     */
    public function setCoef(?float $coef): void
    {
        $this->coef = $coef;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataCad(): ?\DateTime
    {
        return $this->dataCad;
    }

    /**
     * @param \DateTime|null $dataCad
     */
    public function setDataCad(?\DateTime $dataCad): void
    {
        $this->dataCad = $dataCad;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataPcusto(): ?\DateTime
    {
        return $this->dataPcusto;
    }

    /**
     * @param \DateTime|null $dataPcusto
     */
    public function setDataPcusto(?\DateTime $dataPcusto): void
    {
        $this->dataPcusto = $dataPcusto;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataPvenda(): ?\DateTime
    {
        return $this->dataPvenda;
    }

    /**
     * @param \DateTime|null $dataPvenda
     */
    public function setDataPvenda(?\DateTime $dataPvenda): void
    {
        $this->dataPvenda = $dataPvenda;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataUltVenda(): ?\DateTime
    {
        return $this->dataUltVenda;
    }

    /**
     * @param \DateTime|null $dataUltVenda
     */
    public function setDataUltVenda(?\DateTime $dataUltVenda): void
    {
        $this->dataUltVenda = $dataUltVenda;
    }

    /**
     * @return float|null
     */
    public function getDepto(): ?float
    {
        return $this->depto;
    }

    /**
     * @param float|null $depto
     */
    public function setDepto(?float $depto): void
    {
        $this->depto = $depto;
    }

    /**
     * @return null|string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param null|string $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return null|string
     */
    public function getF1(): ?string
    {
        return $this->f1;
    }

    /**
     * @param null|string $f1
     */
    public function setF1(?string $f1): void
    {
        $this->f1 = $f1;
    }

    /**
     * @return null|string
     */
    public function getF10(): ?string
    {
        return $this->f10;
    }

    /**
     * @param null|string $f10
     */
    public function setF10(?string $f10): void
    {
        $this->f10 = $f10;
    }

    /**
     * @return null|string
     */
    public function getF11(): ?string
    {
        return $this->f11;
    }

    /**
     * @param null|string $f11
     */
    public function setF11(?string $f11): void
    {
        $this->f11 = $f11;
    }

    /**
     * @return null|string
     */
    public function getF12(): ?string
    {
        return $this->f12;
    }

    /**
     * @param null|string $f12
     */
    public function setF12(?string $f12): void
    {
        $this->f12 = $f12;
    }

    /**
     * @return null|string
     */
    public function getF2(): ?string
    {
        return $this->f2;
    }

    /**
     * @param null|string $f2
     */
    public function setF2(?string $f2): void
    {
        $this->f2 = $f2;
    }

    /**
     * @return null|string
     */
    public function getF3(): ?string
    {
        return $this->f3;
    }

    /**
     * @param null|string $f3
     */
    public function setF3(?string $f3): void
    {
        $this->f3 = $f3;
    }

    /**
     * @return null|string
     */
    public function getF4(): ?string
    {
        return $this->f4;
    }

    /**
     * @param null|string $f4
     */
    public function setF4(?string $f4): void
    {
        $this->f4 = $f4;
    }

    /**
     * @return null|string
     */
    public function getF5(): ?string
    {
        return $this->f5;
    }

    /**
     * @param null|string $f5
     */
    public function setF5(?string $f5): void
    {
        $this->f5 = $f5;
    }

    /**
     * @return null|string
     */
    public function getF6(): ?string
    {
        return $this->f6;
    }

    /**
     * @param null|string $f6
     */
    public function setF6(?string $f6): void
    {
        $this->f6 = $f6;
    }

    /**
     * @return null|string
     */
    public function getF7(): ?string
    {
        return $this->f7;
    }

    /**
     * @param null|string $f7
     */
    public function setF7(?string $f7): void
    {
        $this->f7 = $f7;
    }

    /**
     * @return null|string
     */
    public function getF8(): ?string
    {
        return $this->f8;
    }

    /**
     * @param null|string $f8
     */
    public function setF8(?string $f8): void
    {
        $this->f8 = $f8;
    }

    /**
     * @return null|string
     */
    public function getF9(): ?string
    {
        return $this->f9;
    }

    /**
     * @param null|string $f9
     */
    public function setF9(?string $f9): void
    {
        $this->f9 = $f9;
    }

    /**
     * @return float|null
     */
    public function getFornec(): ?float
    {
        return $this->fornec;
    }

    /**
     * @param float|null $fornec
     */
    public function setFornec(?float $fornec): void
    {
        $this->fornec = $fornec;
    }

    /**
     * @return float|null
     */
    public function getGrade(): ?float
    {
        return $this->grade;
    }

    /**
     * @param float|null $grade
     */
    public function setGrade(?float $grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return float|null
     */
    public function getMargem(): ?float
    {
        return $this->margem;
    }

    /**
     * @param float|null $margem
     */
    public function setMargem(?float $margem): void
    {
        $this->margem = $margem;
    }

    /**
     * @return float|null
     */
    public function getMargemc(): ?float
    {
        return $this->margemc;
    }

    /**
     * @param float|null $margemc
     */
    public function setMargemc(?float $margemc): void
    {
        $this->margemc = $margemc;
    }

    /**
     * @return null|string
     */
    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    /**
     * @param null|string $modelo
     */
    public function setModelo(?string $modelo): void
    {
        $this->modelo = $modelo;
    }

    /**
     * @return null|string
     */
    public function getOvlProd(): ?string
    {
        return $this->ovlProd;
    }

    /**
     * @param null|string $ovlProd
     */
    public function setOvlProd(?string $ovlProd): void
    {
        $this->ovlProd = $ovlProd;
    }

    /**
     * @return float|null
     */
    public function getPcusto(): ?float
    {
        return $this->pcusto;
    }

    /**
     * @param float|null $pcusto
     */
    public function setPcusto(?float $pcusto): void
    {
        $this->pcusto = $pcusto;
    }

    /**
     * @return float|null
     */
    public function getPprazo(): ?float
    {
        return $this->pprazo;
    }

    /**
     * @param float|null $pprazo
     */
    public function setPprazo(?float $pprazo): void
    {
        $this->pprazo = $pprazo;
    }

    /**
     * @return float|null
     */
    public function getPpromo(): ?float
    {
        return $this->ppromo;
    }

    /**
     * @param float|null $ppromo
     */
    public function setPpromo(?float $ppromo): void
    {
        $this->ppromo = $ppromo;
    }

    /**
     * @return float|null
     */
    public function getPvista(): ?float
    {
        return $this->pvista;
    }

    /**
     * @param float|null $pvista
     */
    public function setPvista(?float $pvista): void
    {
        $this->pvista = $pvista;
    }

    /**
     * @return float|null
     */
    public function getPrazo(): ?float
    {
        return $this->prazo;
    }

    /**
     * @param float|null $prazo
     */
    public function setPrazo(?float $prazo): void
    {
        $this->prazo = $prazo;
    }

    /**
     * @return null|string
     */
    public function getQt01(): ?string
    {
        return $this->qt01;
    }

    /**
     * @param null|string $qt01
     */
    public function setQt01(?string $qt01): void
    {
        $this->qt01 = $qt01;
    }

    /**
     * @return null|string
     */
    public function getQt02(): ?string
    {
        return $this->qt02;
    }

    /**
     * @param null|string $qt02
     */
    public function setQt02(?string $qt02): void
    {
        $this->qt02 = $qt02;
    }

    /**
     * @return null|string
     */
    public function getQt03(): ?string
    {
        return $this->qt03;
    }

    /**
     * @param null|string $qt03
     */
    public function setQt03(?string $qt03): void
    {
        $this->qt03 = $qt03;
    }

    /**
     * @return null|string
     */
    public function getQt04(): ?string
    {
        return $this->qt04;
    }

    /**
     * @param null|string $qt04
     */
    public function setQt04(?string $qt04): void
    {
        $this->qt04 = $qt04;
    }

    /**
     * @return null|string
     */
    public function getQt05(): ?string
    {
        return $this->qt05;
    }

    /**
     * @param null|string $qt05
     */
    public function setQt05(?string $qt05): void
    {
        $this->qt05 = $qt05;
    }

    /**
     * @return null|string
     */
    public function getQt06(): ?string
    {
        return $this->qt06;
    }

    /**
     * @param null|string $qt06
     */
    public function setQt06(?string $qt06): void
    {
        $this->qt06 = $qt06;
    }

    /**
     * @return null|string
     */
    public function getQt07(): ?string
    {
        return $this->qt07;
    }

    /**
     * @param null|string $qt07
     */
    public function setQt07(?string $qt07): void
    {
        $this->qt07 = $qt07;
    }

    /**
     * @return null|string
     */
    public function getQt08(): ?string
    {
        return $this->qt08;
    }

    /**
     * @param null|string $qt08
     */
    public function setQt08(?string $qt08): void
    {
        $this->qt08 = $qt08;
    }

    /**
     * @return null|string
     */
    public function getQt09(): ?string
    {
        return $this->qt09;
    }

    /**
     * @param null|string $qt09
     */
    public function setQt09(?string $qt09): void
    {
        $this->qt09 = $qt09;
    }

    /**
     * @return null|string
     */
    public function getQt10(): ?string
    {
        return $this->qt10;
    }

    /**
     * @param null|string $qt10
     */
    public function setQt10(?string $qt10): void
    {
        $this->qt10 = $qt10;
    }

    /**
     * @return null|string
     */
    public function getQt11(): ?string
    {
        return $this->qt11;
    }

    /**
     * @param null|string $qt11
     */
    public function setQt11(?string $qt11): void
    {
        $this->qt11 = $qt11;
    }

    /**
     * @return null|string
     */
    public function getQt12(): ?string
    {
        return $this->qt12;
    }

    /**
     * @param null|string $qt12
     */
    public function setQt12(?string $qt12): void
    {
        $this->qt12 = $qt12;
    }

    /**
     * @return null|string
     */
    public function getQt13(): ?string
    {
        return $this->qt13;
    }

    /**
     * @param null|string $qt13
     */
    public function setQt13(?string $qt13): void
    {
        $this->qt13 = $qt13;
    }

    /**
     * @return null|string
     */
    public function getQt14(): ?string
    {
        return $this->qt14;
    }

    /**
     * @param null|string $qt14
     */
    public function setQt14(?string $qt14): void
    {
        $this->qt14 = $qt14;
    }

    /**
     * @return null|string
     */
    public function getQt15(): ?string
    {
        return $this->qt15;
    }

    /**
     * @param null|string $qt15
     */
    public function setQt15(?string $qt15): void
    {
        $this->qt15 = $qt15;
    }

    /**
     * @return float|null
     */
    public function getQtdeMes(): ?float
    {
        return $this->qtdeMes;
    }

    /**
     * @param float|null $qtdeMes
     */
    public function setQtdeMes(?float $qtdeMes): void
    {
        $this->qtdeMes = $qtdeMes;
    }

    /**
     * @return int|null
     */
    public function getRecordNumber(): ?int
    {
        return $this->recordNumber;
    }

    /**
     * @param int|null $recordNumber
     */
    public function setRecordNumber(?int $recordNumber): void
    {
        $this->recordNumber = $recordNumber;
    }

    /**
     * @return float|null
     */
    public function getReduzido(): ?float
    {
        return $this->reduzido;
    }

    /**
     * @param float|null $reduzido
     */
    public function setReduzido(?float $reduzido): void
    {
        $this->reduzido = $reduzido;
    }

    /**
     * @return null|string
     */
    public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    /**
     * @param null|string $referencia
     */
    public function setReferencia(?string $referencia): void
    {
        $this->referencia = $referencia;
    }

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param null|string $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return float|null
     */
    public function getSubdepto(): ?float
    {
        return $this->subdepto;
    }

    /**
     * @param float|null $subdepto
     */
    public function setSubdepto(?float $subdepto): void
    {
        $this->subdepto = $subdepto;
    }

    /**
     * @return float|null
     */
    public function getUltVender(): ?float
    {
        return $this->ultVender;
    }

    /**
     * @param float|null $ultVender
     */
    public function setUltVender(?float $ultVender): void
    {
        $this->ultVender = $ultVender;
    }

    /**
     * @return null|string
     */
    public function getUnidade(): ?string
    {
        return $this->unidade;
    }

    /**
     * @param null|string $unidade
     */
    public function setUnidade(?string $unidade): void
    {
        $this->unidade = $unidade;
    }

    /**
     * @return null|string
     */
    public function getCst(): ?string
    {
        return $this->cst;
    }

    /**
     * @param null|string $cst
     */
    public function setCst(?string $cst): void
    {
        $this->cst = $cst;
    }

    /**
     * @return null|string
     */
    public function getFracionado(): ?string
    {
        return $this->fracionado;
    }

    /**
     * @param null|string $fracionado
     */
    public function setFracionado(?string $fracionado): void
    {
        $this->fracionado = $fracionado;
    }

    /**
     * @return float|null
     */
    public function getIcms(): ?float
    {
        return $this->icms;
    }

    /**
     * @param float|null $icms
     */
    public function setIcms(?float $icms): void
    {
        $this->icms = $icms;
    }

    /**
     * @return null|string
     */
    public function getNcm(): ?string
    {
        return $this->ncm;
    }

    /**
     * @param null|string $ncm
     */
    public function setNcm(?string $ncm): void
    {
        $this->ncm = $ncm;
    }

    /**
     * @return null|string
     */
    public function getTipoTrib(): ?string
    {
        return $this->tipoTrib;
    }

    /**
     * @param null|string $tipoTrib
     */
    public function setTipoTrib(?string $tipoTrib): void
    {
        $this->tipoTrib = $tipoTrib;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtUltalt(): ?\DateTime
    {
        return $this->dtUltalt;
    }

    /**
     * @param \DateTime|null $dtUltalt
     */
    public function setDtUltalt(?\DateTime $dtUltalt): void
    {
        $this->dtUltalt = $dtUltalt;
    }

    /**
     * @return null|string
     */
    public function getMesano(): ?string
    {
        return $this->mesano;
    }

    /**
     * @param null|string $mesano
     */
    public function setMesano(?string $mesano): void
    {
        $this->mesano = $mesano;
    }

    /**
     * @return null|string
     */
    public function getUnidadeCorr(): ?string
    {
        return $this->unidadeCorr;
    }

    /**
     * @param null|string $unidadeCorr
     */
    public function setUnidadeCorr(?string $unidadeCorr): void
    {
        $this->unidadeCorr = $unidadeCorr;
    }

    /**
     * @return CfgEstabelecimento
     */
    public function getEstabelecimento(): CfgEstabelecimento
    {
        return $this->estabelecimento;
    }

    /**
     * @param CfgEstabelecimento $estabelecimento
     */
    public function setEstabelecimento(CfgEstabelecimento $estabelecimento): void
    {
        $this->estabelecimento = $estabelecimento;
    }

    /**
     * @return SecUser
     */
    public function getUserUpdated(): SecUser
    {
        return $this->userUpdated;
    }

    /**
     * @param SecUser $userUpdated
     */
    public function setUserUpdated(SecUser $userUpdated): void
    {
        $this->userUpdated = $userUpdated;
    }

    /**
     * @return SecUser
     */
    public function getUserInserted(): SecUser
    {
        return $this->userInserted;
    }

    /**
     * @param SecUser $userInserted
     */
    public function setUserInserted(SecUser $userInserted): void
    {
        $this->userInserted = $userInserted;
    }


}
