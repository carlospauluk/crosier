<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * SecGroup
 *
 * @ORM\Table(name="sec_group", uniqueConstraints={@ORM\UniqueConstraint(name="UK9fr1lrbpsxlyb9rl3syf74nbh", columns={"groupname"})}, indexes={@ORM\Index(name="FKovu458abiv1cf5wym3djre40n", columns={"user_inserted_id"}), @ORM\Index(name="FKh503ubl5jh82brgr5b60k1sk9", columns={"estabelecimento_id"}), @ORM\Index(name="FKi2fsmkm38lp21wvnxqaqkeavi", columns={"user_updated_id"})})
 * @ORM\Entity
 */
class SecGroup
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
     * @var string
     *
     * @ORM\Column(name="groupname", type="string", length=90, nullable=false)
     */
    private $groupname;

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
     *   @ORM\JoinColumn(name="user_updated_id", referencedColumnName="id")
     * })
     */
    private $userUpdated;

    /**
     * @var \SecUser
     *
     * @ORM\ManyToOne(targetEntity="SecUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     * })
     */
    private $userInserted;


}
