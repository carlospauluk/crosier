<?php
namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\EntityId;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entidade 'Group'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Security\GroupRepository")
 * @ORM\Table(name="sec_group")
 * @ORM\HasLifecycleCallbacks()
 */
class Group extends EntityId
{

    /**
     *
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="groupname", type="string", length=90, unique=true)
     */
    private $username;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="sec_group_role",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    public function __construct() {
        $this->roles = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    /**
     *
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
    
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
    

   
}

