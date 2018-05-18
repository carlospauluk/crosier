<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade Operadora de Cartões.
 * Ex.: RDCARD, CIELO, STONE.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\BandeiraCartaoRepository")
 * @ORM\Table(name="fin_bandeira_cartao")
 *
 * @author Carlos Eduardo Pauluk
 */
class OperadoraCartao extends EntityId
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_id", nullable=true)
     */
    private $carteira;
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

    public function getCarteira(): ?Carteira
    {
        return $this->carteira;
    }

    public function setCarteira(?Carteira $carteira)
    {
        $this->carteira = $carteira;
    }


    
}
