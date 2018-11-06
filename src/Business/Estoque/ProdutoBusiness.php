<?php

namespace App\Business\Estoque;

use App\Entity\Estoque\Produto;
use App\EntityOC\OcCategory;
use App\EntityOC\OcManufacturer;
use App\EntityOC\OcProduct;
use App\EntityOC\OcProductDescription;
use App\EntityOC\OcProductToCategory;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProdutoBusiness
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getOcProductArrayByProduto(?Produto $produto)
    {
        if (!$produto or !$produto->getNaLojaVirtual()) return null;

        $sql = "SELECT oc_product_id FROM est_produto_oc_product WHERE est_produto_id = ?";
        $rsm = new ResultSetMapping();
        $qry = $this->doctrine->getEntityManager()->createNativeQuery($sql, $rsm);
        $qry->setParameter(1, $produto->getId());
        $rsm->addScalarResult('oc_product_id', 'oc_product_id');
        $oc_product_id = $qry->getResult();


        if ($oc_product_id) {

            $oc_product_id = $oc_product_id[0]['oc_product_id'];

            $ocProduct = $this->doctrine->getEntityManager('oc')->getRepository(OcProduct::class)->find($oc_product_id);
            $ocProductDescription = $this->doctrine->getEntityManager('oc')->getRepository(OcProductDescription::class)->findOneBy(['productId' => $ocProduct->getProductId()]);
            $ocManufacturer = $this->doctrine->getEntityManager('oc')->getRepository(OcManufacturer::class)->find($ocProduct->getManufacturerId());


            $ocProductToCategory = $this->doctrine->getEntityManager('oc')->getRepository(OcProductToCategory::class)->findOneBy(['productId' => $ocProduct->getProductId()]);

            $ocCategory = null;
            if ($ocProductToCategory) {
                $ocCategory = $this->doctrine->getEntityManager('oc')->getRepository(OcCategory::class)->find($ocProductToCategory->getCategoryId());
            }

            $ocProductArray = [
                'id' => $ocProduct->getProductId(),
                'modelo' => $ocProduct->getModel(),
                'sku' => $ocProduct->getSku(),
                'qtde' => $ocProduct->getQuantity(),
                'marca_id' => $ocManufacturer->getManufacturerId(),
                'depto_id' => $ocCategory->getCategoryId(),
                'preco' => $ocProduct->getPrice(),
                'dimensaoL' => $ocProduct->getWeight(),
                'dimensaoC' => $ocProduct->getLength(),
                'dimensaoA' => $ocProduct->getHeight(),
                'status' => $ocProduct->isStatus(),
                'titulo' => $ocProductDescription->getName(),
                'descricao' => $ocProductDescription->getDescription()
            ];

            /**
             * oc_product
             *      model
             *      sku
             *      quantity
             *      manufacturer_id
             *      price
             *      weight
             *      length
             *      width
             *      height
             *      status (0/1)
             * oc_product_description
             *      name - produto
             *      description - descricao
             */

            return $ocProductArray;

        } else {
            return null;
        }

    }

    public function saveOcProduct($ocProductArray) {

    }

}