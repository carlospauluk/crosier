<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade 'ImportExtratoCabec'.
 *
 * Registra as relações de-para entre campos da fin_movimentacao e campos do CSV.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\ImportExtratoCabecRepository")
 * @ORM\Table(name="fin_import_extrato_cabec")
 */
class ImportExtratoCabec extends EntityId
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="tipo_extrato", type="string", nullable=false, length=100)
     */
    private $tipoExtrato;

    /**
     *
     * @ORM\Column(name="campo_sistema", type="string", nullable=false, length=100)
     */
    private $campoSistema;

    /**
     *
     * @ORM\Column(name="campos_cabecalho", type="string", nullable=false, length=200)
     */
    private $camposCabecalho;

    /**
     *
     * @ORM\Column(name="formato", type="string", nullable=true, length=100)
     */
    private $formato;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTipoExtrato()
    {
        return $this->tipoExtrato;
    }

    /**
     * @param mixed $tipoExtrato
     */
    public function setTipoExtrato($tipoExtrato): void
    {
        $this->tipoExtrato = $tipoExtrato;
    }


    /**
     * @return mixed
     */
    public function getCampoSistema()
    {
        return $this->campoSistema;
    }

    /**
     * @param mixed $campoSistema
     */
    public function setCampoSistema($campoSistema): void
    {
        $this->campoSistema = $campoSistema;
    }

    /**
     * @return mixed
     */
    public function getCamposCabecalho()
    {
        return $this->camposCabecalho;
    }

    /**
     * @param mixed $camposCabecalho
     */
    public function setCamposCabecalho($camposCabecalho): void
    {
        $this->camposCabecalho = $camposCabecalho;
    }

    /**
     * @return mixed
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * @param mixed $formato
     */
    public function setFormato($formato): void
    {
        $this->formato = $formato;
    }


}

