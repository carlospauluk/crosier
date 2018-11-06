<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcFaixaCep
 *
 * @ORM\Table(name="oc_faixa_cep")
 * @ORM\Entity
 */
class OcFaixaCep
{
    /**
     * @var int
     *
     * @ORM\Column(name="faixa_cep_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $faixaCepId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep_inicial", type="string", length=9, nullable=true)
     */
    private $cepInicial;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep_final", type="string", length=9, nullable=true)
     */
    private $cepFinal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="peso_minimo", type="string", length=20, nullable=true)
     */
    private $pesoMinimo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="peso_maximo", type="string", length=20, nullable=true)
     */
    private $pesoMaximo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_minimo", type="string", length=20, nullable=true)
     */
    private $totalMinimo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="preco", type="string", length=20, nullable=true)
     */
    private $preco;


}
