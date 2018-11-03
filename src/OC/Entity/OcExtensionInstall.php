<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcExtensionInstall
 *
 * @ORM\Table(name="oc_extension_install")
 * @ORM\Entity
 */
class OcExtensionInstall
{
    /**
     * @var int
     *
     * @ORM\Column(name="extension_install_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $extensionInstallId;

    /**
     * @var int
     *
     * @ORM\Column(name="extension_download_id", type="integer", nullable=false)
     */
    private $extensionDownloadId;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
