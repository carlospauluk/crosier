<?php

namespace App\Entity\Fiscal;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade para mensagens de retorno da Receita Federal.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Fiscal\MsgRetornoRFRepository")
 * @ORM\Table(name="fis_msg_retorno_rf")
 */
class MsgRetornoRF extends EntityId
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
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'codigo' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="mensagem", type="string", nullable=false, length=2000)
     * @Assert\NotBlank(message="O campo 'mensagem' deve ser informado")
     */
    private $mensagem;

    /**
     *
     * @ORM\Column(name="versao", type="string", nullable=false, length=10)
     * @Assert\NotBlank(message="O campo 'versao' deve ser informado")
     */
    private $versao;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getMensagem()
    {
        return $this->mensagem;
    }

    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    public function getVersao()
    {
        return $this->versao;
    }

    public function setVersao($versao)
    {
        $this->versao = $versao;
    }
}