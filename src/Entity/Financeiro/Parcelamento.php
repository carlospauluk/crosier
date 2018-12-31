<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade 'Parcelamento'.
 *
 * @author Carlos Eduardo Pauluk
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\ParcelamentoRepository")
 * @ORM\Table(name="fin_parcelamento")
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
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0)
     */
    private $valorTotal;

    /**
     *
     * @var Movimentacao[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Movimentacao",
     *      mappedBy="parcelamento"
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

    public function addParcela(?Movimentacao $movimentacao): void
    {
        $movimentacao->setParcelamento($this);
        if (!$this->parcelas->contains($movimentacao)) {
            $this->parcelas->add($movimentacao);
        }
    }

    public function removeParcela(Movimentacao $movimentacao): void
    {
        $movimentacao->setParcelamento(null);
        $this->parcelas->removeElement($movimentacao);
    }

    /**
     * Método auxiliar.
     *
     * @return number
     */
    public function getQtdeParcelas()
    {
        return $this->parcelas->count();
    }

    /**
     * Método auxiliar.
     *
     * @return number
     */
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
        $this->setValorTotal($valorTotal);
    }

    /**
     *
     * @return Collection|Movimentacao[]
     */
    public function getParcelas(): Collection
    {
        return $this->parcelas;
    }
}

