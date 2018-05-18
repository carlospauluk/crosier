<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Utils\StringUtils;
use Doctrine\Common\Collections\Collection;

class Movimentacao
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    
    
    
    // ---------------------------------------------------------------------------------------
    // ---------- CAMPOS PARA "CHEQUE"
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Banco")
     * @ORM\JoinColumn(name="cheque_banco_id", nullable=true)
     */
    private $chequeBanco;
    
    /**
     * Código da agência (sem o dígito verificador).
     *
     * @ORM\Column(name="cheque_agencia", type="string", nullable=true, length=30)
     */
    private $chequeAgencia;
    
    /**
     * Número da conta no banco (não segue um padrão).
     *
     * @ORM\Column(name="cheque_conta", type="string", nullable=true, length=30)
     */
    private $chequeConta;
    
    /**
     * Número da conta no banco (não segue um padrão).
     *
     * @ORM\Column(name="cheque_num_cheque", type="string", nullable=true, length=30)
     */
    private $chequeNumCheque;
    
    
    
    // ---------------------------------------------------------------------------------------
    // ---------- CAMPOS PARA "RECORRÊNCIA"
    
    /**
     *
     * @ORM\Column(name="recorrente", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $recorrente = false;

    /**
     *
     * @ORM\Column(name="recorr_frequencia", type="string", nullable=false, length=50)
     * @Assert\NotBlank()
     */
    private $recorrFrequencia;

    /**
     *
     * @ORM\Column(name="recorr_tipo_repet", type="string", nullable=false, length=50)
     * @Assert\NotBlank()
     */
    private $recorrTipoRepet;

    /**
     * Utilizar 32 para marcar o último dia do mês.
     *
     * FIXME: meio burro isso (podia usar o 31 mesmo).
     *
     * @ORM\Column(name="dia", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 32)
     */
    private $recorrDia;

    /**
     * Utilizado para marcar a variação em relação ao dia em que seria o vencimento.
     * Exemplo: dia=32 (último dia do mês) + variacao=-2 >>> 2 dias antes do último dia do mês
     *
     * FIXME: meio burro isso (podia usar o 31 mesmo).
     *
     * @ORM\Column(name="recorr_variacao", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $recorrVariacao;
    
    // ---------------------------------------------------------------------------------------
}

