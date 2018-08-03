<?php
namespace App\Entity\Fiscal;

use App\Entity\Base\EntityId;
use App\Entity\Vendas\Venda;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * 
 *
 * @ORM\Entity(repositoryClass="App\Repository\Fiscal\NotaFiscalVendaRepository")
 * @ORM\Table(name="fis_nf_venda")
 */
class NotaFiscalVenda extends EntityId
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Fiscal\NotaFiscal")
     * @ORM\JoinColumn(name="nota_fiscal_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Nota_fiscal' deve ser informado")
     *
     * @var $notaFiscal NotaFiscal
     */
    private $notaFiscal;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vendas\Venda")
     * @ORM\JoinColumn(name="venda_id", nullable=false)
     *
     * @var $venda Venda
     */
    private $venda;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
    }

    public function getNotaFiscal(): ?NotaFiscal
    {
        return $this->notaFiscal;
    }

    public function setNotaFiscal(?NotaFiscal $notaFiscal)
    {
        $this->notaFiscal = $notaFiscal;
    }

    public function getVenda(): ?Venda
    {
        return $this->venda;
    }

    public function setVenda(?Venda $venda)
    {
        $this->venda = $venda;
    }
}