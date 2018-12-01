<?php

namespace App\Entity\Estoque;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\FornecedorOcManufacturerRepository")
 * @ORM\Table(name="est_fornecedor_oc_manufacturer")
 */
class FornecedorOcManufacturer
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Fornecedor")
     * @ORM\JoinColumn(name="est_fornecedor_id", nullable=false)
     *
     * @var $fornecedor Fornecedor
     */
    private $fornecedor;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="oc_manufacturer_id", type="integer", nullable=false)
     */
    private $manufacturerId;

    /**
     * @return Fornecedor
     */
    public function getFornecedor(): Fornecedor
    {
        return $this->fornecedor;
    }

    /**
     * @param Fornecedor $fornecedor
     */
    public function setFornecedor(Fornecedor $fornecedor): void
    {
        $this->fornecedor = $fornecedor;
    }

    /**
     * @return int
     */
    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }

    /**
     * @param int $manufacturerId
     */
    public function setManufacturerId($manufacturerId): void
    {
        $this->manufacturerId = $manufacturerId;
    }


}