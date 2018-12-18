<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * SecUser
 *
 * @ORM\Table(name="sec_user", uniqueConstraints={@ORM\UniqueConstraint(name="UK5ctbdrlf3eismye20vsdtk8w8", columns={"username", "estabelecimento_id"})}, indexes={@ORM\Index(name="FK220fu1ge4dpwuc5i4s43h5a5j", columns={"estabelecimento_id"}), @ORM\Index(name="FKasdpuv9jq6o44vihexwwngide", columns={"user_updated_id"}), @ORM\Index(name="FK4rioll4k3wwpqw34tlg311yfc", columns={"user_inserted_id"}), @ORM\Index(name="FKjebdusj3li8ahjhf83nf32tmh", columns={"group_id"})})
 * @ORM\Entity
 */
class SecUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     */
    private $inserted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var bool
     *
     * @ORM\Column(name="ativo", type="boolean", nullable=false)
     */
    private $ativo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=90, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=90, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="senha", type="string", length=90, nullable=false)
     */
    private $senha;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=90, nullable=false)
     */
    private $username;

    /**
     * @var \CfgEstabelecimento
     *
     * @ORM\ManyToOne(targetEntity="CfgEstabelecimento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estabelecimento_id", referencedColumnName="id")
     * })
     */
    private $estabelecimento;

    /**
     * @var \SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     * })
     */
    private $userInserted;

    /**
     * @var \SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_updated_id", referencedColumnName="id")
     * })
     */
    private $userUpdated;

    /**
     * @var \SecGroup
     *
     * @ORM\ManyToOne(targetEntity="SecGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;


}
