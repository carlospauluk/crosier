<?php
namespace App\Entity\Base;

use App\Entity\Financeiro\TipoPessoa;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade 'Pessoa'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Base\PessoaRepository")
 * @ORM\Table(name="bon_pessoa")
 */
class Pessoa extends EntityId
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
     * @ORM\Column(name="tipo_pessoa", type="string", nullable=false, length=15)
     * @Assert\NotBlank()
     *
     * @var $tipoPessoa TipoPessoa
     */
    private $tipoPessoa = "PESSOA_JURIDICA";

    /**
     * CPF ou CNPJ, somente números (não usar pontuação).
     *
     * @ORM\Column(name="documento", type="string", nullable=true, length=50)
     */
    private $documento;

    /**
     * Para Pessoa Jurídica é a Razão Social.
     *
     * @ORM\Column(name="nome", type="string", nullable=false, length=300)
     * @Assert\Range(min=2, max=300)
     */
    private $nome;

    /**
     * Somente para pessoa jurídica.
     *
     * @ORM\Column(name="nome_fantasia", type="string", nullable=true, length=300)
     * @Assert\Range(min=2, max=300)
     */
    private $nomeFantasia;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTipoPessoa()
    {
        return $this->tipoPessoa;
    }

    public function setTipoPessoa($tipoPessoa)
    {
        $this->tipoPessoa = $tipoPessoa;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getNomeFantasia()
    {
        return $this->nomeFantasia;
    }

    public function setNomeFantasia($nomeFantasia)
    {
        $this->nomeFantasia = $nomeFantasia;
    }
}

