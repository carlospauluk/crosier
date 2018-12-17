<?php

namespace App\EntityEKT;

use Doctrine\ORM\Mapping as ORM;

/**
 * SecRole
 *
 * @ORM\Table(name="sec_role", uniqueConstraints={@ORM\UniqueConstraint(name="UK2n3qbrc5tu07q0xyi94caepcp", columns={"role"})}, indexes={@ORM\Index(name="FKi2brdlce0ggj7aog4xn6gptq1", columns={"user_inserted_id"}), @ORM\Index(name="FKpunrs1h5wlkebn5pxlqamxlyw", columns={"estabelecimento_id"}), @ORM\Index(name="FKelrwbvmjqkfey84nrn9vnm3qe", columns={"user_updated_id"})})
 * @ORM\Entity
 */
class SecRole
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
     * @ORM\Column(name="descricao", type="string", length=90, nullable=false)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=90, nullable=false)
     */
    private $role;

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

    /**
     * @var \CfgEstabelecimento
     *
     * @ORM\ManyToOne(targetEntity="CfgEstabelecimento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estabelecimento_id", referencedColumnName="id")
     * })
     */
    private $estabelecimento;


}
