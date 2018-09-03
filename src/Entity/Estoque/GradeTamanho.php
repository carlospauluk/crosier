<?php
namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\GradeTamanhoRepository")
 * @ORM\Table(name="est_grade_tamanho")
 * @ORM\HasLifecycleCallbacks()
 */
class GradeTamanho extends EntityId
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
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'ordem' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $ordem;

    /**
     *
     * @ORM\Column(name="tamanho", type="string", nullable=false, length=100)
     * @Assert\NotBlank(message="O campo 'tamanho' deve ser informado")
     */
    private $tamanho;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Grade")
     * @ORM\JoinColumn(name="grade_id", nullable=false)
     *
     * @var $grade Grade
     */
    private $grade;

    /**
     *
     * @ORM\Column(name="posicao", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'posicao' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $posicao;

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

    public function getOrdem()
    {
        return $this->ordem;
    }

    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    public function getTamanho()
    {
        return $this->tamanho;
    }

    public function setTamanho($tamanho)
    {
        $this->tamanho = $tamanho;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade)
    {
        $this->grade = $grade;
    }

    public function getPosicao()
    {
        return $this->posicao;
    }

    public function setPosicao($posicao)
    {
        $this->posicao = $posicao;
    }
}
