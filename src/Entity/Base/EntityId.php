<?php
namespace App\Entity\Base;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

class EntityId
{

    /**
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     */
    public $inserted;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     */
    public $updated;

    /**
     * @ORM\Column(name="estabelecimento_id", type="integer", nullable=false)
     * @Assert\NotNull()
     */
    public $estabelecimento;

    public function getInserted()
    {
        return $this->inserted;
    }

    public function setInserted($inserted)
    {
        $this->inserted = $inserted;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getEstabelecimento()
    {
        return $this->estabelecimento;
    }

    public function setEstabelecimento($estabelecimento)
    {
        $this->estabelecimento = $estabelecimento;
    }
}