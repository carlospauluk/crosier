<?php
namespace App\Entity\Financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\BancoRepository")
 * @ORM\Table(name="fin_banco")
 */
class Banco extends EntityId
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="codigo_banco", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $codigoBanco;

    /**
     *
     * @ORM\Column(name="nome", type="string", nullable=false, length=200)
     * @Assert\NotBlank()
     */
    private $nome;

    /**
     *
     * @ORM\Column(name="utilizado", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $utilizado = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    public function setCodigoBanco($codigoBanco)
    {
        $this->codigoBanco = $codigoBanco;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getUtilizado()
    {
        return $this->utilizado;
    }

    public function setUtilizado($utilizado)
    {
        $this->utilizado = $utilizado;
    }
}

