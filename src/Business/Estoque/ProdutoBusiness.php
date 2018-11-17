<?php

namespace App\Business\Estoque;

use App\Entity\Estoque\FornecedorOcManufacturer;
use App\Entity\Estoque\GradeOcOption;
use App\Entity\Estoque\GradeTamanhoOcOptionValue;
use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoOcProduct;
use App\Entity\Estoque\SubdeptoOcCategory;
use App\EntityOC\OcCategory;
use App\EntityOC\OcManufacturer;
use App\EntityOC\OcProduct;
use App\EntityOC\OcProductDescription;
use App\EntityOC\OcProductImage;
use App\EntityOC\OcProductOption;
use App\EntityOC\OcProductOptionValue;
use App\EntityOC\OcProductToCategory;
use App\EntityOC\OcProductToStore;
use App\Utils\StringUtils;
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

        $produtoOcProduct = $this->doctrine->getRepository(ProdutoOcProduct::class)->findOneby(['produto' => $produto]);

        if ($produtoOcProduct and $produtoOcProduct->getProductId()) {
            $ocProductId = $produtoOcProduct->getProductId();

            $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($ocProductId);
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
     * @param Produto $produto
     * @param $ocProductArray
     * @throws \Exception
     */
    public function saveOcProduct(Produto $produto, $ocProductArray = null)
    {
        $ocEntityManager = $this->doctrine->getEntityManager('oc');
        try {
            $editando = false;
            $ocEntityManager->beginTransaction();

            // Se o produto já foi vinculado de outra forma a um oc_product, será uma edição mesmo sem vir valores pelo $ocProductArray
            if (!$ocProductArray) {
                $ocProductArray = $this->getOcProductArrayByProduto($produto);
            }

            // Se está atualizando um já existente
            if ($ocProductArray['id']) {
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

            if ($editando) {
                // Se estiver editando, pega do array (que veio do form)
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
            } else {
                // Se está inserindo, pega os dados do est_produto
                $ocProduct->setSku($produto->getReduzido());
                $ocProduct->setModel($produto->getSubdepto()->getNome());
                $ocProduct->setHeight(0);
                $ocProduct->setLength(0);
                $ocProduct->setWidth(0);
                $ocProduct->setWeight(0);
                $ocProduct->setStatus(0); // inativo, a princípio
                $ocProduct->setStockStatusId(5);
                $ocProduct->setPrice($produto->getPrecoAtual()->getPrecoPrazo());

                if ($produto->getFornecedor()) {
                    $fornecedor2manufacturer = $this->doctrine->getRepository(FornecedorOcManufacturer::class)->findOneBy(['fornecedor' => $produto->getFornecedor()]);
                    if ($fornecedor2manufacturer) {
                        $ocProduct->setManufacturerId($fornecedor2manufacturer->getManufacturerId());
                    }
                } else {
                    $ocProduct->setManufacturerId(0); // Não informado
                }
                $ocProduct->setQuantity(0);

                $ocProductDescription->setName($produto->getDescricao());
                $ocProductDescription->setMetaTitle($produto->getDescricao());
                $ocProductDescription->setDescription($produto->getDescricao());

                if ($produto->getSubdepto()) {
                    $subdepto2category = $this->doctrine->getRepository(SubdeptoOcCategory::class)->findOneBy(['subdepto' => $produto->getSubdepto()]);
                    if ($subdepto2category) {
                        $ocProductToCategory->setCategoryId($subdepto2category->getCategoryId());
                    }
                } else {
                    $ocProductToCategory->setCategoryId(1);
                }
            }

            $agora = new \DateTime('now');
            $ocProduct->setDateModified($agora);


            if ($editando) {
                $ocEntityManager->merge($ocProduct);
                $ocEntityManager->merge($ocProductDescription);
                $ocEntityManager->merge($ocProductToCategory);
                $ocEntityManager->flush();
            } else {
                $ocProduct->setDateAvailable($agora);
                $ocProduct->setDateAdded($agora);

                $ocEntityManager->persist($ocProduct);
                $ocEntityManager->flush();

                $ocProductDescription->setProductId($ocProduct->getProductId());
                $ocProductDescription->setLanguageId(2); // fixo na base
                $ocEntityManager->persist($ocProductDescription);
                $ocEntityManager->flush();

                $ocProductToCategory->setProductId($ocProduct->getProductId());
                $ocEntityManager->persist($ocProductToCategory);
                $ocEntityManager->flush();

                $ocProductToStore = new OcProductToStore();
                $ocProductToStore->setProductId($ocProduct->getProductId());
                $ocProductToStore->setStoreId(0); // fixo na base
                $ocEntityManager->persist($ocProductToStore);
                $ocEntityManager->flush();

                // de-para entre est_produto e oc_product
                $produtoOcProduct = new ProdutoOcProduct();
                $produtoOcProduct->setProduto($produto);
                $produtoOcProduct->setProductId($ocProduct->getProductId());
                $this->doctrine->getEntityManager()->persist($produtoOcProduct);
                $this->doctrine->getEntityManager()->flush();
            }

            // de-para entre est_grade e oc_option
            $gradeOcOption = $this->doctrine->getRepository(GradeOcOption::class)->findOneBy(['grade' => $produto->getGrade()]);
            if (!$gradeOcOption) {
                throw new \Exception('de-para entre est_grade e oc_option não encontrado');
            }
            // Verifica se por acaso não tem já para não precisar inserir novamente
            $ocProductOption = $ocEntityManager->getRepository(OcProductOption::class)
                ->findOneBy(['productId' => $ocProduct->getProductId(),
                    'optionId' => $gradeOcOption->getOptionId()]);
            if (!$ocProductOption) {
                // Se ainda não tem, insere
                $ocProductOption = new OcProductOption();
                $ocProductOption->setOptionId($gradeOcOption->getOptionId());
                $ocProductOption->setProductId($ocProduct->getProductId());
                $ocProductOption->setValue(''); // pra q serve??
                $ocProductOption->setRequired(1);
                $ocEntityManager->persist($ocProductOption);
                $ocEntityManager->flush();
            }

            // percorre todos os saldos de cada gradeTamanho do produto
            foreach ($produto->getSaldos() as $saldo) {
                // de-para entre est_grade_tamanho e oc_option_value
                $gradeTamanhoOcOptionValue = $this->doctrine->getRepository(GradeTamanhoOcOptionValue::class)->findOneBy(['gradeTamanho' => $saldo->getGradeTamanho()]);
                if (!$gradeTamanhoOcOptionValue) {
                    throw new \Exception('de-para entre est_grade_tamanho e oc_option_value não encontrado');
                }
                $ocProductOptionValue = null;
                // Verifico se essa opção já não existe (pra não precisar inserir novamente)
                $ocProductOptionValue = $ocEntityManager->getRepository(OcProductOptionValue::class)
                    ->findOneBy(['productId' => $ocProduct->getProductId(),
                        'optionValueId' => $gradeTamanhoOcOptionValue->getOptionValueId()]);

                if (!$ocProductOptionValue) {
                    $ocProductOptionValue = new OcProductOptionValue();
                    $ocProductOptionValue->setProductId($ocProduct->getProductId());
                    $ocProductOptionValue->setSubtract(1);
                    $ocProductOptionValue->setPricePrefix('+');
                    $ocProductOptionValue->setPoints(0);
                    $ocProductOptionValue->setPointsPrefix('+');
                    $ocProductOptionValue->setWeightPrefix('+');
                    $ocProductOptionValue->setWeight(0);
                }

                $ocProductOptionValue->setProductOptionId($ocProductOption->getProductOptionId());
                $ocProductOptionValue->setOptionId($gradeOcOption->getOptionId());
                $ocProductOptionValue->setOptionValueId($gradeTamanhoOcOptionValue->getOptionValueId());
                $ocProductOptionValue->setQuantity($saldo->getQtde());
                $ocProductOptionValue->setPrice('');

                if (!$ocProductOptionValue->getProductOptionValueId()) {
                    $ocEntityManager->persist($ocProductOptionValue);
                } else {
                    $ocEntityManager->merge($ocProductOptionValue);
                }
                $ocEntityManager->flush();
            }


            // Por fim, removo as productOptionValue e productOption que não condizam com a grade atual do produto
            $ocProductOptionValues = $ocEntityManager->getRepository(OcProductOptionValue::class)->findBy(['productId' => $ocProduct->getProductId()]);
            foreach ($ocProductOptionValues as $ocProductOptionValue) {
                if ($ocProductOptionValue->getOptionId() != $gradeOcOption->getOptionId()) {
                    $ocEntityManager->remove($ocProductOptionValue);
                }
            }
            $ocEntityManager->flush();

            $ocProductOptions = $ocEntityManager->getRepository(OcProductOption::class)->findBy(['productId' => $ocProduct->getProductId()]);
            foreach ($ocProductOptions as $ocProductOption) {
                if ($ocProductOption->getOptionId() !== $gradeOcOption->getOptionId()) {
                    $ocEntityManager->remove($ocProductOption);
                }
            }

            $ocEntityManager->flush();
            $ocEntityManager->commit();

        } catch (\Exception $e) {
            $ocEntityManager->rollback();
            throw new \Exception('Erro ao salvar produto oc', 0, $e);
        }

        try {
            $this->saveImages($produto, $ocProduct);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao salvar imagens do produto', 0, $e);
        }


    }


    public function saveImages(Produto $produto, OcProduct $ocProduct)
    {
        $ocEntityManager = $this->doctrine->getEntityManager('oc');
        $productId = $ocProduct->getProductId();
        // $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($productId);


        $crosierfolder = getenv('CROSIER_FOLDER');
        $ocProductImagesFolder = $crosierfolder . '/product-images/';

        $fornecedoresFolders = scandir($ocProductImagesFolder);
        $fornecedorFolder = null;
        foreach ($fornecedoresFolders as $ff) {
            if (strlen($ff) >= strlen($produto->getFornecedor()->getCodigo()) and substr($ff, 0, strlen($produto->getFornecedor()->getCodigo())) == $produto->getFornecedor()->getCodigo()) {
                $fornecedorFolder = $ff;
                break;
            }
        }
        if (!$fornecedorFolder) {
            $fornecedorFolder = $ocProductImagesFolder . $produto->getFornecedor()->getCodigo() . '-' . StringUtils::strToFilenameStr($produto->getFornecedor()->getPessoa()->getNomeFantasia());;
            mkdir($fornecedorFolder, 0755);
        }

        $ents = scandir($ocProductImagesFolder . $fornecedorFolder);
        $produtoFolder = null;
        // tenta encontrar uma pasta que tenha seu nome começando pelo reduzido
        foreach ($ents as $ent) {
            if (substr($ent, 0, strlen($produto->getReduzido())) == $produto->getReduzido()) {
                $produtoFolder = $ent;
                break;
            }
        }
        if (!$produtoFolder) {
            $nome = StringUtils::strToFilenameStr($produto->getDescricao());
            $produtoFolder = $produto->getReduzido() . '-' . $nome;
            mkdir($ocProductImagesFolder . $fornecedorFolder . $produtoFolder, 0755);
        }

        $produtoFolder_compl = $ocProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder;

        $ftpServer = getenv('OC_FTP_SERVER');
        $ftpUsername = getenv('OC_FTP_USERNAME');
        $ftpPassword = getenv('OC_FTP_PASSWORD');
        $ftpProductImagesFolder = getenv('OC_FTP_PRODUCT_IMAGE_FOLDER');
        $httpProductImagesFolder = getenv('OC_HTTP_PRODUCT_IMAGE_FOLDER');

        $ftpConn = ftp_connect($ftpServer);
        $logged = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
        if (!$logged) {
            throw new \Exception('Erro ao conectar ao FTP', 0, $e);
        }
        if (!ftp_chdir($ftpConn, $ftpProductImagesFolder)) {
            throw new \Exception('Erro ao abrir pasta das imagens no FTP', 0, $e);
        }


        @ftp_mkdir($ftpConn, $ftpProductImagesFolder . '/' . $fornecedorFolder);
        @ftp_mkdir($ftpConn, $ftpProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder);
        if (!ftp_chdir($ftpConn, $ftpProductImagesFolder . '/' . $fornecedorFolder)) {
            throw new \Exception('Erro em chdir(fornecedorFolder)');
        }
        if (!ftp_chdir($ftpConn, $ftpProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder)) {
            throw new \Exception('Erro em chdir(produtoFolder)');
        }

        // Percorre as imagens da pasta

        $remoteFiles = ftp_mlsd($ftpConn, '.');
        $imagens = scandir($produtoFolder_compl);
        foreach ($imagens as $img) {
            $existe = false;
            if (is_dir($produtoFolder_compl . '/' . $img)) continue;
            $pathParts = pathinfo($produtoFolder_compl . '/' . $img);

            foreach ($remoteFiles as $remoteFile) {
                if ($img == $remoteFile['name']) {
                    $existe = true;
                    $md5Remote = md5_file($httpProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder . '/' . $img);
                    $md5Local = md5_file($produtoFolder_compl . '/' . $img);
                    // O arquivo foi alterado, reenvia...
                    if ($md5Remote !== $md5Local) {
                        ftp_delete($ftpConn, $remoteFile['name']);
                        ftp_put($ftpConn, $remoteFile['img'], $img, FTP_BINARY);
                    }
                }
            }

            // Não achou o arquivo
            if (!$existe) {
                ftp_put($ftpConn, $img, $produtoFolder_compl . '/' . $img, FTP_BINARY);

                if (intval($pathParts['filename']) == 1) {
                    $ocProduct->setImage('catalog/produtos/' . $fornecedorFolder . '/' . $produtoFolder . '/' . $img);
                    $ocEntityManager->persist($ocProduct);
                    $ocEntityManager->flush();
                } else {
                    $ocProductImage = new OcProductImage();
                    $ocProductImage->setImage('catalog/produtos/' . $fornecedorFolder . '/' . $produtoFolder . '/' . $img);
                    $ocProductImage->setProductId($productId);
                    $ocProductImage->setSortOrder(intval($pathParts['filename']));
                    $ocEntityManager->persist($ocProductImage);
                    $ocEntityManager->flush();
                }
            }
        }
        ftp_close($ftpConn);


    }

}