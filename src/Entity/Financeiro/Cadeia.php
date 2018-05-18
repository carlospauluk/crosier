<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Entity\Financeiro\Movimentacao;

/**
 * Entidade 'Cadeia de Movimentações'.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class Cadeia extends EntityId
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
     * @var Movimentacao[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Movimentacao",
     *      mappedBy="cadeia",
     *      orphanRemoval=true
     * )
     */
    private $movimentacoes;

    /**
     *
     * Se for vinculante, ao deletar uma movimentação da cadeia todas são deletadas, ver trigger trg_ad_delete_cadeia.
     *
     * @ORM\Column(name="vinculante", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $vinculante = false;

    /**
     *
     * Se for fechada, não é possível incluir outras movimentações na cadeia.
     *
     * @ORM\Column(name="fechada", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $vinculante = false;

    public function __construct()
    {
        $this->movimentacoes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return Collection|Movimentacao[]
     */
    public function getMovimentacoes(): Collection
    {
        return $this->movimentacoes;
    }

    public function addMovimentacao(?Movimentacao $movimentacao): void
    {
        $movimentacao->setCadeia($this);
        if (! $this->movimentacoes->contains($movimentacao)) {
            $this->movimentacoes->add($movimentacao);
        }
    }

    public function removeMovimentacao(Movimentacao $movimentacao): void
    {
        $movimentacao->setCadeia(null);
        $this->movimentacoes->removeElement($movimentacao);
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
}

