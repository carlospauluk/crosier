<?php

namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use App\Entity\Estoque\GradeTamanho;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\ConfeccaoItemQtdeRepository")
 * @ORM\Table(name="prod_confeccao_item_qtde")
 */
class ConfeccaoItemQtde extends EntityId
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
     * @ORM\Column(name="qtde", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'qtde' deve ser numérico")
     */
    private $qtde;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\ConfeccaoItem")
     * @ORM\JoinColumn(name="confeccao_item_id", nullable=false)
     *
     * @var $confeccaoItem ConfeccaoItem
     */
    private $confeccaoItem;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\GradeTamanho")
     * @ORM\JoinColumn(name="grade_tamanho_id", nullable=false)
     *
     * @var $gradeTamanho GradeTamanho
     */
    private $gradeTamanho;

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

    public function getQtde()
    {
        return $this->qtde;
    }

    public function setQtde($qtde)
    {
        $this->qtde = $qtde;
    }

    public function getConfeccaoItem(): ?ConfeccaoItem
    {
        return $this->confeccaoItem;
    }

    public function setConfeccaoItem(?ConfeccaoItem $confeccaoItem)
    {
        $this->confeccaoItem = $confeccaoItem;
    }

    public function getGradeTamanho(): ?GradeTamanho
    {
        return $this->gradeTamanho;
    }

    public function setGradeTamanho(?GradeTamanho $gradeTamanho)
    {
        $this->gradeTamanho = $gradeTamanho;
    }
}