<?php
namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade Bandeira de Cartão.
 * Ex.: MASTER MAESTRO, MASTER, VISA ELECTRON, VISA, etc.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\BandeiraCartaoRepository")
 * @ORM\Table(name="fin_bandeira_cartao")
 *
 * @author Carlos Eduardo Pauluk
 */
class BandeiraCartao extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=40)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Modo")
     * @ORM\JoinColumn(nullable=false)
     * @var $modo Modo
     */
    private $modo;

    /**
     * Para marcar diferentes nomes que podem ser utilizados para definir uma bandeira (ex.: MAESTRO ou MASTER MAESTRO ou M MAESTRO).
     * 
     * @ORM\Column(name="labels", type="string", nullable=false, length=2000)
     * @Assert\NotBlank()
     */
    private $labels;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getModo(): ?Modo
    {
        return $this->modo;
    }

    public function setModo(?Modo $modo)
    {
        $this->modo = $modo;
    }

    public function getLabels()
    {
        return $this->labels;
    }

    public function setLabels($labels)
    {
        $this->labels = $labels;
    }
}
