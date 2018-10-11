<?php

namespace App\Entity\Base;

use App\Entity\Config\Estabelecimento;
use App\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class EntityId
{

    /**
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    public $inserted;

    /**
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    public $updated;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Config\Estabelecimento", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="estabelecimento_id", nullable=false)
     *
     */
    public $estabelecimento;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="user_inserted_id", nullable=false)
     *
     */
    public $userInserted;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="user_updated_id", nullable=false)
     *
     */
    public $userUpdated;

    public function getInserted(): ?\DateTime
    {
        return $this->inserted;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function getEstabelecimento(): ?Estabelecimento
    {
        return $this->estabelecimento;
    }

    public function getUserInserted(): ?User
    {
        return $this->userInserted;
    }

    public function getUserUpdated(): ?User
    {
        return $this->userUpdated;
    }

    public function setInserted(?\DateTime $inserted)
    {
        $this->inserted = $inserted;
    }

    public function setUpdated(?\DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function setEstabelecimento(?Estabelecimento $estabelecimento)
    {
        $this->estabelecimento = $estabelecimento;
    }

    public function setUserInserted(?User $userInserted)
    {
        $this->userInserted = $userInserted;
    }

    public function setUserUpdated(?User $userUpdated)
    {
        $this->userUpdated = $userUpdated;
    }

}