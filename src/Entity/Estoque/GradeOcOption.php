<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\GradeOcOptionRepository")
 * @ORM\Table(name="est_grade_oc_option")
 */
class GradeOcOption
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Grade")
     * @ORM\JoinColumn(name="est_grade_id", nullable=false)
     *
     * @var $grade Grade
     */
    private $grade;

    /**
     * @ORM\Id
     * @ORM\Column(name="oc_option_id", type="integer", nullable=false)
     */
    private $optionId;

    /**
     * @return Grade
     */
    public function getGrade(): Grade
    {
        return $this->grade;
    }

    /**
     * @param Grade $grade
     */
    public function setGrade(Grade $grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * @param mixed $optionId
     */
    public function setOptionId($optionId): void
    {
        $this->optionId = $optionId;
    }


}
