<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Entity\Financeiro\Movimentacao;

/**
 * Entidade 'Parcelamento'.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class Parcelamento extends EntityId
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
     * @ORM\Column(name="valor_total", type="decimal", nullable=false, precision=15, scale=2)
     */
    private $valorTotal;

    /**
     *
     * @var Movimentacao[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Movimentacao",
     *      mappedBy="cadeia",
     *      orphanRemoval=true
     * )
     */
    private $parcelas;

    public function __construct()
    {
        $this->parcelas = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    /**
     *
     * @return Collection|Movimentacao[]
     */
    public function getParcelas(): Collection
    {
        return $this->parcelas;
    }

    public function addParcela(?Movimentacao $movimentacao): void
    {
        $movimentacao->setCadeia($this);
        if (! $this->parcelas->contains($movimentacao)) {
            $this->parcelas->add($movimentacao);
        }
    }

    public function removeParcela(Movimentacao $movimentacao): void
    {
        $movimentacao->setCadeia(null);
        $this->parcelas->removeElement($movimentacao);
    }

    public function getVinculante()
    {
        return $this->vinculante;
    }

    public function setVinculante($vinculante)
    {
        $this->vinculante = $vinculante;
    }

    public function getVinculante()
    {
        return $this->vinculante;
    }

    public function setVinculante($vinculante)
    {
        $this->vinculante = $vinculante;
    }

    public function getQtdeParcelas()
    {
        return $this->parcelas->count();
    }

    public function recalcularParcelas()
    {
        $valorTotal = 0.0;
        if (($this->getParcelas() != null) && ($this->getParcelas()->count() > 0)) {
            foreach ($this->getParcelas() as $parcela) {
                if ($parcela->getValorTotal() != null) {
                    $valorTotal += $parcela->getValorTotal();
                }
            }
        }
        return $valorTotal;
    }
}

