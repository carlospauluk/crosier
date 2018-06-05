<?php
namespace App\Entity\Base;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

class EntityId
{

    /**
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     * @Assert\NotNull(message = "O campo 'inserted' precisa ser informado")
     * @Assert\Type("\DateTime")
     */
    public $inserted;

    /**
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Assert\NotNull(message = "O campo 'updated' precisa ser informado")
     * @Assert\Type("\DateTime")
     */
    public $updated;

    /**
     *
     * @ORM\Column(name="estabelecimento_id", type="bigint", nullable=false)
     * @Assert\NotNull(message = "O campo 'estabelecimento_id' precisa ser informado")
     */
    public $estabelecimento;

    /**
     *
     * @ORM\Column(name="user_inserted_id", type="bigint", nullable=false)
     * @Assert\NotNull(message = "O campo 'user_inserted_id' precisa ser informado")
     */
    public $userInserted;

    /**
     *
     * @ORM\Column(name="user_updated_id", type="bigint", nullable=false)
     * @Assert\NotNull(message = "O campo 'user_updated_id' precisa ser informado")
     */
    public $userUpdated;

    public function getInserted()
    {
        return $this->inserted;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getEstabelecimento()
    {
        return $this->estabelecimento;
    }

    public function getUserInserted()
    {
        return $this->userInserted;
    }

    public function getUserUpdated()
    {
        return $this->userUpdated;
    }

    public function setInserted($inserted)
    {
        $this->inserted = $inserted;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function setEstabelecimento($estabelecimento)
    {
        $this->estabelecimento = $estabelecimento;
    }

    public function setUserInserted($userInserted)
    {
        $this->userInserted = $userInserted;
    }

    public function setUserUpdated($userUpdated)
    {
        $this->userUpdated = $userUpdated;
    }
}