<?php

namespace App\Entity\Base;

use ReflectionClass;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="estabelecimento_id", type="bigint", nullable=false)
     */
    public $estabelecimento;

    /**
     *
     * @ORM\Column(name="user_inserted_id", type="bigint", nullable=false)
     */
    public $userInserted;

    /**
     *
     * @ORM\Column(name="user_updated_id", type="bigint", nullable=false)
     */
    public $userUpdated;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
    }

    public function getInserted(): ?\DateTime
    {
        return $this->inserted;
    }

    public function getUpdated(): ?\DateTime
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

    public function setInserted(?\DateTime $inserted)
    {
        $this->inserted = $inserted;
    }

    public function setUpdated(?\DateTime $updated)
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

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->handleUppercaseFields();
        $this->setInserted(new \DateTime('now'));
        $this->setEstabelecimento(1);
        $this->setUserInserted(1);
        $this->setUpdated(new \DateTime('now'));
        $this->setUserUpdated(1);
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->handleUppercaseFields();
        $this->setUpdated(new \DateTime());
        $this->setUserUpdated(1);
    }


    private function handleUppercaseFields() {
        $uppercaseFieldsJson = file_get_contents('../src/Entity/uppercaseFields.json');
        $uppercaseFields = json_decode($uppercaseFieldsJson);
        $class = str_replace('\\', '_', get_class($this));
        $reflectionClass = new ReflectionClass(get_class($this));
        $campos = isset($uppercaseFields->$class) ? $uppercaseFields->$class : array()  ;
        foreach ($campos as $field) {
            $property = $reflectionClass->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($this, mb_strtoupper($property->getValue($this)));
        }
    }
}