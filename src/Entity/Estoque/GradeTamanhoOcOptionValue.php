<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\GradeTamanhoOcOptionValueRepository")
 * @ORM\Table(name="est_grade_tamanho_oc_option_value")
 */
class GradeTamanhoOcOptionValue
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\GradeTamanho")
     * @ORM\JoinColumn(name="est_grade_tamanho_id", nullable=false)
     *
     * @var $gradeTamanho GradeTamanho
     */
    private $gradeTamanho;

    /**
     * @ORM\Id
     * @ORM\Column(name="oc_option_value_id", type="integer", nullable=false)
     */
    private $optionValueId;

    /**
     * @return GradeTamanho
     */
    public function getGradeTamanho(): GradeTamanho
    {
        return $this->gradeTamanho;
    }

    /**
     * @param GradeTamanho $gradeTamanho
     */
    public function setGradeTamanho(GradeTamanho $gradeTamanho): void
    {
        $this->gradeTamanho = $gradeTamanho;
    }

    /**
     * @return mixed
     */
    public function getOptionValueId()
    {
        return $this->optionValueId;
    }

    /**
     * @param mixed $optionValueId
     */
    public function setOptionValueId($optionValueId): void
    {
        $this->optionValueId = $optionValueId;
    }


}
