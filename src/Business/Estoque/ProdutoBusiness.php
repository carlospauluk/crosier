<?php

namespace App\Business\Estoque;

use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoOcProduct;
use App\EntityOC\OcCategory;
use App\EntityOC\OcManufacturer;
use App\EntityOC\OcProduct;
use App\EntityOC\OcProductDescription;
use App\EntityOC\OcProductToCategory;
use App\EntityOC\OcProductToStore;
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

        $ocEntityManager = $this->doctrine->getEntityManager('oc');

        $sql = "SELECT oc_product_id FROM est_produto_oc_product WHERE est_produto_id = ?";
        $rsm = new ResultSetMapping();
        $qry = $this->doctrine->getEntityManager()->createNativeQuery($sql, $rsm);
        $qry->setParameter(1, $produto->getId());
        $rsm->addScalarResult('oc_product_id', 'oc_product_id');
        $oc_product_id = $qry->getResult();


        if ($oc_product_id) {

            $oc_product_id = $oc_product_id[0]['oc_product_id'];

            $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($oc_product_id);
            $ocProductDescription = $ocEntityManager->getRepository(OcProductDescription::class)->findOneBy(['productId' => $ocProduct->getProductId()]);
            $ocManufacturer = $ocEntityManager->getRepository(OcManufacturer::class)->find($ocProduct->getManufacturerId());


            $ocProductToCategory = $ocEntityManager->getRepository(OcProductToCategory::class)->findOneBy(['productId' => $ocProduct->getProductId()]);

            $ocCategory = null;
            if ($ocProductToCategory) {
                $ocCategory = $ocEntityManager->getRepository(OcCategory::class)->find($ocProductToCategory->getCategoryId());
            }

            $ocProductArray = [
                'id' => $ocProduct->getProductId(),
                'model' => $ocProduct->getModel(),
                'sku' => $ocProduct->getSku(),
                'quantity' => $ocProduct->getQuantity(),
                'manufacturerId' => $ocManufacturer->getManufacturerId(),
                'categoryId' => $ocCategory->getCategoryId(),
                'price' => $ocProduct->getPrice(),
                'stockStatusId' => $ocProduct->getStockStatusId(),
                'weight' => $ocProduct->getWeight(),
                'height' => $ocProduct->getHeight(),
                'length' => $ocProduct->getLength(),
                'width' => $ocProduct->getWidth(),
                'status' => $ocProduct->isStatus(),
                'name' => $ocProductDescription->getName(),
                'description' => $ocProductDescription->getDescription()
            ];

            return $ocProductArray;

        } else {
            return null;
        }

    }


    /**
     * Salva ou atualiza os dados do produto na base do opencart.
     * @param $ocProductArray
     * @throws \Exception
     */
    public function saveOcProduct(Produto $produto, $ocProductArray)
    {

        $editando = false;
        $ocEntityManager = $this->doctrine->getEntityManager('oc');
        $ocEntityManager->beginTransaction();
        if (isset($ocProductArray['id'])) {
            $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($ocProductArray['id']);
            if (!$ocProduct) {
                throw new \Exception('ocProduct não encontrado');
            }
            $ocProductDescription = $ocEntityManager->getRepository(OcProductDescription::class)->findOneBy(['productId' => $ocProduct->getProductId()]);
            if (!$ocProductDescription) {
                throw new \Exception('ocProductDescription não encontrado');
            }
            $ocProductToCategory = $ocEntityManager->getRepository(OcProductToCategory::class)->findOneBy(['productId' => $ocProduct->getProductId()]);
            if (!$ocProductToCategory) {
                throw new \Exception('ocProductToCategory não encontrado');
            }

            $editando = true;
        } else {
            $ocProduct = new OcProduct();
            $ocProductDescription = new OcProductDescription();
            $ocProductToCategory = new OcProductToCategory();
        }

        $ocProduct->setSku($ocProductArray['sku']);
        $ocProduct->setModel($ocProductArray['model']);
        $ocProduct->setHeight($ocProductArray['height']);
        $ocProduct->setLength($ocProductArray['length']);
        $ocProduct->setWidth($ocProductArray['width']);
        $ocProduct->setWeight($ocProductArray['weight'] ? $ocProductArray['weight'] : '');
        $ocProduct->setStatus($ocProductArray['status']);
        $ocProduct->setStockStatusId($ocProductArray['stockStatusId']);
        $ocProduct->setPrice($ocProductArray['price']);
        $ocProduct->setManufacturerId($ocProductArray['manufacturerId']);
        $ocProduct->setQuantity($ocProductArray['quantity']);


        $ocProductDescription->setName($ocProductArray['name']);
        $ocProductDescription->setMetaTitle($ocProductArray['name']);
        $ocProductDescription->setDescription($ocProductArray['description']);

        $ocProductToCategory->setCategoryId($ocProductArray['categoryId']);


        $agora = new \DateTime('now');
        $ocProduct->setDateModified($agora);

        try {
            if ($editando) {
                $ocEntityManager->merge($ocProduct);
                $ocEntityManager->merge($ocProductDescription);
                $ocEntityManager->merge($ocProductToCategory);
            } else {
                $ocProduct->setDateAvailable($agora);
                $ocProduct->setDateAdded($agora);

                $ocEntityManager->persist($ocProduct);
                $ocEntityManager->flush();
                $ocProductDescription->setProductId($ocProduct->getProductId());
                $ocProductDescription->setLanguageId(2); // fixo na base
                $ocEntityManager->persist($ocProductDescription);

                $ocProductToCategory->setProductId($ocProduct->getProductId());
                $ocEntityManager->persist($ocProductToCategory);

                $ocProductToStore = new OcProductToStore();
                $ocProductToStore->setProductId($ocProduct->getProductId());
                $ocProductToStore->setStoreId(0); // fixo na base
                $ocEntityManager->persist($ocProductToStore);

                $produtoOcProduct = new ProdutoOcProduct();
                $produtoOcProduct->setProduto($produto);
                $produtoOcProduct->setProductId($ocProduct->getProductId());
                $this->doctrine->getEntityManager()->persist($produtoOcProduct);
                $this->doctrine->getEntityManager()->flush();

            }
            $ocEntityManager->flush();

            $ocEntityManager->commit();
        } catch (\Exception $e) {
            $ocEntityManager->rollback();
            throw new \Exception('Erro ao salvar produto oc', 0, $e);
        }


    }

}