<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcSetting
 *
 * @ORM\Table(name="oc_setting")
 * @ORM\Entity
 */
class OcSetting
{
    /**
     * @var int
     *
     * @ORM\Column(name="setting_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $settingId;

    /**
     * @var int
     *
     * @ORM\Column(name="store_id", type="integer", nullable=false)
     */
    private $storeId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=128, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=128, nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=false)
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="serialized", type="boolean", nullable=false)
     */
    private $serialized;


}
