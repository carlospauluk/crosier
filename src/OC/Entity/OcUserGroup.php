<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcUserGroup
 *
 * @ORM\Table(name="oc_user_group")
 * @ORM\Entity
 */
class OcUserGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="permission", type="text", length=65535, nullable=false)
     */
    private $permission;


}
