<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcModification
 *
 * @ORM\Table(name="oc_modification")
 * @ORM\Entity
 */
class OcModification
{
    /**
     * @var int
     *
     * @ORM\Column(name="modification_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $modificationId;

    /**
     * @var int
     *
     * @ORM\Column(name="extension_install_id", type="integer", nullable=false)
     */
    private $extensionInstallId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=64, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=64, nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=32, nullable=false)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=false)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="xml", type="text", length=16777215, nullable=false)
     */
    private $xml;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
