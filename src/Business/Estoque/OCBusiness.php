<?php

namespace App\Business\Estoque;

use App\Business\BaseBusiness;
use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\FornecedorOcManufacturer;
use App\Entity\Estoque\GradeOcOption;
use App\Entity\Estoque\GradeTamanhoOcOptionValue;
use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoOcProduct;
use App\Entity\Estoque\Subdepto;
use App\Entity\Estoque\SubdeptoOcCategory;
use App\EntityOC\OcAttributeDescription;
use App\EntityOC\OcCategory;
use App\EntityOC\OcCategoryFilter;
use App\EntityOC\OcFilter;
use App\EntityOC\OcFilterDescription;
use App\EntityOC\OcFilterGroupDescription;
use App\EntityOC\OcManufacturer;
use App\EntityOC\OcProduct;
use App\EntityOC\OcProductAttribute;
use App\EntityOC\OcProductDescription;
use App\EntityOC\OcProductFilter;
use App\EntityOC\OcProductImage;
use App\EntityOC\OcProductOption;
use App\EntityOC\OcProductOptionValue;
use App\EntityOC\OcProductToCategory;
use App\EntityOC\OcProductToStore;
use App\Exception\ViewException;
use App\Utils\FTP\FTPUtils;
use App\Utils\Repository\WhereBuilder;
use App\Utils\StringUtils;
use Doctrine\ORM\ORMException;

class OCBusiness extends BaseBusiness
{

    private $produtoBusiness;

    private $ftpUtils;

    private $ftpConn;


    /**
     * Monta um array 'de-para' entre est_produto e oc_product.
     *
     * @param Fornecedor $fornecedor
     * @return array
     * @throws ViewException
     */
    public function compareEstProdutosOcProducts(Fornecedor $fornecedor)
    {
        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
        // Retorna todos os produtos atuais do $fornecedor.
        $rs = [];
        $subdeptos = $this->getDoctrine()->getRepository(Subdepto::class)->getSubdeptosAtivosByFornecedor($fornecedor);
        foreach ($subdeptos as $subdepto) {
            $r = [];
            $r['subdepto']['id'] = $subdepto->getId();
            $r['subdepto']['nome'] = $subdepto->getNome();
            $produtos = $this->getDoctrine()->getRepository(Produto::class)->findBy(['fornecedor' => $fornecedor, 'subdepto' => $subdepto, 'atual' => true], ['descricao' => 'ASC']);
            foreach ($produtos as $produto) {
                $rProduto = [];

                $rProduto['produto']['id'] = $produto->getId();
                $rProduto['produto']['descricao'] = $produto->getDescricao();
                $rProduto['produto']['saldo'] = $produto->getSaldoTotal();
                $rProduto['produto']['preco'] = $produto->getPrecoAtual()->getPrecoVenda();

                $ocProduct = $this->getOcProductByProduto($produto);
                if ($ocProduct) {
                    $ocProductDescription = $ocEntityManager->getRepository(OcProductDescription::class)->findOneBy(['productId' => $ocProduct->getProductId()]);
                    $rProduto['ocProduct']['id'] = $ocProductDescription->getProductId();
                    $rProduto['ocProduct']['name'] = $ocProductDescription->getName();

                    $ocProductOptionValues = $ocEntityManager->getRepository(OcProductOptionValue::class)
                        ->findBy(['productId' => $ocProduct->getProductId()]);
                    $total = 0;
                    foreach ($ocProductOptionValues as $ocProductOptionValue) {
                        $total = bcadd($total, $ocProductOptionValue->getQuantity());
                    }
                    $rProduto['ocProduct']['saldo'] = $total;
                    $rProduto['ocProduct']['preco'] = $ocProduct->getPrice();
                    $rProduto['ocProduct']['status'] = $ocProduct->isStatus() ? 'ATIVO' : 'INATIVO';
                }
                $r['produtos'][] = $rProduto;

            }
            $rs[] = $r;
        }
        return $rs;
    }

    /**
     * @param Produto $produto
     * @return OcProduct|null|object
     * @throws ViewException
     */
    public function getOcProductByProduto(Produto $produto)
    {
        try {
            $produtoOcProduct = $this->getDoctrine()->getRepository(ProdutoOcProduct::class)->findOneBy(['produto' => $produto]);
            if ($produtoOcProduct) {
                $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
                $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($produtoOcProduct->getProductId());
                return $ocProduct;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            throw new ViewException('Erro ao pesquisar ocProduct por produto', 0, $e);
        }
    }

    /**
     * @param Fornecedor $fornecedor
     * @param Subdepto|null $subdepto
     * @throws ViewException
     */
    public function regerarOcProducts(Fornecedor $fornecedor, Subdepto $subdepto = null)
    {
        try {
            $produtos = $this->getDoctrine()->getRepository(Produto::class)->findBy(['fornecedor' => $fornecedor, 'subdepto' => $subdepto, 'atual' => true], ['descricao' => 'ASC']);
            foreach ($produtos as $produto) {
                $this->saveOcProduct($produto);
            }
        } catch (\Exception $e) {
            throw new ViewException('Erro ao regerar produtos para a loja virtual.');
        }
    }

    /**
     * @param Fornecedor $fornecedor
     * @param Subdepto|null $subdepto
     * @throws ViewException
     */
    public function corrigirNomesEDescricoes(Fornecedor $fornecedor, Subdepto $subdepto = null)
    {
        try {
            $produtos = $this->getDoctrine()->getRepository(Produto::class)->findBy(['fornecedor' => $fornecedor, 'subdepto' => $subdepto, 'atual' => true], ['descricao' => 'ASC']);
            foreach ($produtos as $produto) {
                $this->corrigirOcNomeEDescricao($produto);
            }
        } catch (\Exception $e) {
            throw new ViewException('Erro ao corrigir nomes e descrições produtos para a loja virtual.');
        }
    }

    /**
     * Array com dados idênticos ao que vem do form.
     *
     * @param Produto|null $produto
     * @return array|null
     * @throws ViewException
     */
    public function getOcProductArrayByProduto(?Produto $produto)
    {
        if (!$produto) return null;

        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');

        $ocProduct = $this->getOcProductByProduto($produto);

        if ($ocProduct) {
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

            $ocProductFilters = $ocEntityManager->getRepository(OcProductFilter::class)->findBy(['productId' => $ocProduct->getProductId()]);
            foreach ($ocProductFilters as $ocProductFilter) {
                $ocProductArray['filters'][] = $ocProductFilter->getFilterId();
            }

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
        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
        try {
            $editando = false;
            $ocEntityManager->beginTransaction();

            // O ocProductArray vem do save pelo form. Quando não vem, é pq o save tá vindo de outro lugar,
            // como por ex. do regerar ou do corrigirSaldosOCCommand
            if (!$ocProductArray) {
                // verifica se já existe o produto, aí monta o array como se tivesse vindo do form.
                $ocProduct = $this->getOcProductByProduto($produto);
                if ($ocProduct) {
                    $ocProductArray = $this->getOcProductArrayByProduto($produto);
                }
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
                // subdepto no opencart
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
                $ocProduct->setSku($produto->getReduzido()); // $ocProductArray['sku']);   SEMPRE VINCULA
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
                    $fornecedor2manufacturer = $this->getDoctrine()->getRepository(FornecedorOcManufacturer::class)->findOneBy(['fornecedor' => $produto->getFornecedor()]);
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
                    $subdepto2category = $this->getDoctrine()->getRepository(SubdeptoOcCategory::class)->findOneBy(['subdepto' => $produto->getSubdepto()]);
                    if ($subdepto2category) {
                        $ocProductToCategory->setCategoryId($subdepto2category->getCategoryId());
                    }
                } else {
                    $ocProductToCategory->setCategoryId(1); // categoria padrão (INDEFINIDA)
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
                $this->getDoctrine()->getEntityManager()->persist($produtoOcProduct);
                $this->getDoctrine()->getEntityManager()->flush();
            }

            // A est_grade no opencart é um oc_product_option
            // de-para entre est_grade e oc_option
            $gradeOcOption = $this->getDoctrine()->getRepository(GradeOcOption::class)->findOneBy(['grade' => $produto->getGrade()]);
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

            // A est_grade_tamanho no opencart é um oc_product_option_value
            // percorre todos os saldos de cada gradeTamanho do produto
            foreach ($produto->getSaldos() as $saldo) {
                if (!$saldo->getSelec()) continue;
                // de-para entre est_grade_tamanho e oc_option_value
                $gradeTamanhoOcOptionValue = $this->getDoctrine()->getRepository(GradeTamanhoOcOptionValue::class)->findOneBy(['gradeTamanho' => $saldo->getGradeTamanho()]);
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


            // Por fim, removo as productOptionValue e productOption que não condizam com a grade atual do produto,
            // caso a grade do est_produto tenha sido alterada
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
            // Salva os filtros do produto
            $filters = isset($ocProductArray['filters']) ? $ocProductArray['filters'] : null;
            $this->saveOcProductFilter($produto, $ocProduct, $filters);
        } catch (\Exception $e) {
            $this->getLogger()->error('Erro ao salvar filtros');
            $this->getLogger()->error($e->getMessage());
            throw new \Exception('Erro ao salvar filtros.', 0, $e);
        }


        try {
            $produto->setNaLojaVirtual(true);
            $this->getDoctrine()->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new ViewException('Erro ao marcar produto "naLojaVirtual"', 0, $e);
        }


    }


    /**
     * Salva as imagens do produto a partir da pasta 'crosierfolder'.
     *
     * @param Produto $produto
     * @return int
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws ViewException
     * @throws \Exception
     */
    public function saveImages(Produto $produto, $ftpConn = null)
    {
        $ftpAberto = $ftpConn != null;

        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');

        $ocProduct = $this->getOcProductByProduto($produto);

        $crosierfolder = getenv('CROSIER_FOLDER');
        $ocProductImagesFolder = $crosierfolder . '/product-images';

        $fornecedoresFolders = scandir($ocProductImagesFolder);
        $fornecedorFolder = null;
        foreach ($fornecedoresFolders as $ff) {
            if (strlen($ff) >= strlen($produto->getFornecedor()->getCodigo()) and substr($ff, 0, strlen($produto->getFornecedor()->getCodigo())) == $produto->getFornecedor()->getCodigo()) {
                $fornecedorFolder = $ff;
                break;
            }
        }
        if (!$fornecedorFolder) {
            $fornecedorFolder = $ocProductImagesFolder . '/' . $produto->getFornecedor()->getCodigo() . '-' . StringUtils::strToFilenameStr($produto->getFornecedor()->getPessoa()->getNomeFantasia());
            mkdir($fornecedorFolder, 0777);
        }

        $ents = scandir($ocProductImagesFolder . '/' . $fornecedorFolder);
        $produtoFolder = null;
        // tenta encontrar uma pasta que tenha seu nome começando pelo reduzido
        foreach ($ents as $ent) {
            if (strpos($ent, $produto->getReduzido()) !== FALSE) {
                $produtoFolder = $ent;
                break;
            }
        }
        if (!$produtoFolder) {
            $nome = StringUtils::strToFilenameStr($produto->getDescricao());
            $produtoFolder = $nome . '-' . $produto->getReduzido();
            mkdir($ocProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder, 0777);
        }
        $produtoFolder_compl = $ocProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder;


        $ftpProductImagesFolder = getenv('OC_FTP_PRODUCT_IMAGE_FOLDER');
        $httpProductImagesFolder = getenv('OC_HTTP_PRODUCT_IMAGE_FOLDER');


        if (!$ftpAberto) {
            $ftpServer = getenv('OC_FTP_SERVER');
            $ftpUsername = getenv('OC_FTP_USERNAME');
            $ftpPassword = getenv('OC_FTP_PASSWORD');
            $ftpConn = ftp_connect($ftpServer);
        }
        $logged = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
        if (!$logged) {
            throw new ViewException('Erro ao conectar ao FTP', 0);
        }
        if (!ftp_chdir($ftpConn, $ftpProductImagesFolder)) {
            throw new ViewException('Erro ao abrir pasta das imagens no FTP', 0);
        }

        // manda criar com supressão de erros (caso exista, não retorna o erro)
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
        $qtdeImagens = 0;

        $ocProductImages = $ocEntityManager->getRepository(OcProductImage::class)->findBy(['productId' => $ocProduct->getProductId()]);
        $ocProductImages_existemNaBase = [];
        $qtdeImagensAtualizadas = 0;
        foreach ($imagens as $img) {
            // Só aceita arquivos com o formato '99.extt'
            if (!preg_match('/^\d{2}\.{1}\w{3,4}$/', $img)) continue;

            $qtdeImagens++;
            $existe = false;
            $pathParts = pathinfo($produtoFolder_compl . '/' . $img);

            foreach ($remoteFiles as $remoteFile) {
                if ($img == $remoteFile['name']) {
                    $existe = true;
                    $md5Remote = md5_file($httpProductImagesFolder . '/' . $fornecedorFolder . '/' . $produtoFolder . '/' . $img);
                    $md5Local = md5_file($produtoFolder_compl . '/' . $img);
                    // O arquivo foi alterado, reenvia...
                    if ($md5Remote !== $md5Local) {
                        ftp_delete($ftpConn, $remoteFile['name']);
                        ftp_put($ftpConn, $img, $produtoFolder_compl . '/' . $img, FTP_BINARY);
                    }
                    break;
                }
            }
            // Não achou o arquivo
            if (!$existe) {
                ftp_put($ftpConn, $img, $produtoFolder_compl . '/' . $img, FTP_BINARY);
            }

            $sortOrder = intval($pathParts['filename']);
            $fileNameRemotoCompl = 'catalog/produtos/' . $fornecedorFolder . '/' . $produtoFolder . '/' . $img;

            if ($sortOrder == 1) {
                // a primeira imagem sempre vai como principal do produto (registro direto na oc_product)
                if ($ocProduct->getImage() != $fileNameRemotoCompl) {
                    $ocProduct->setImage($fileNameRemotoCompl);
                    $ocEntityManager->persist($ocProduct);
                    $ocEntityManager->flush();
                }
            } else {
                // as demais vão na oc_product_image
                $existeNaBase = false;

                foreach ($ocProductImages as $ocProductImage) {
                    if ($ocProductImage->getSortOrder() == $sortOrder) {
                        $ocProductImages_existemNaBase[] = $ocProductImage;
                        $existeNaBase = true;
                        // se, por acaso, o nome do arquivo está errado, altera na base.
                        if ($ocProductImage->getImage() != $fileNameRemotoCompl) {
                            $ocProductImage->setImage($fileNameRemotoCompl);
                            $ocEntityManager->persist($ocProduct);
                            $ocEntityManager->flush();
                        }
                    }
                }
                if (!$existeNaBase) {
                    $ocProductImage = new OcProductImage();
                    $ocProductImage->setImage($fileNameRemotoCompl);
                    $ocProductImage->setProductId($ocProduct->getProductId());
                    $ocProductImage->setSortOrder(intval($pathParts['filename']));
                    $ocEntityManager->persist($ocProductImage);
                    $ocEntityManager->flush();
                    $ocProductImages_existemNaBase[] = $ocProductImage;
                }
            }
            $qtdeImagensAtualizadas++;
        }

        // Por fim, remove todos os oc_product_image que não existam...
        foreach ($ocProductImages as $ocProductImage) {
            if (!in_array($ocProductImage, $ocProductImages_existemNaBase)) {
                $ocEntityManager->remove($ocProductImage);
                $ocEntityManager->flush();
            }
        }
        $ocProductImages = $ocEntityManager->getRepository(OcProductImage::class)->findBy(['productId' => $ocProduct->getProductId()]);
        // ...e também os arquivos que estejam no FTP porém que não existam na base
        $remoteFiles = ftp_mlsd($ftpConn, '.');
        foreach ($remoteFiles as $remoteFile) {
            if (strpos($remoteFile['type'], 'dir') !== false) continue;
            $arquivoRemotoExisteNaBase = false;
            $baseName_product = pathinfo($ocProduct->getImage())['basename'];
            if ($baseName_product == $remoteFile['name']) {
                $arquivoRemotoExisteNaBase = true;
            } else {
                foreach ($ocProductImages as $ocProductImage) {
                    $baseName_productImage = pathinfo($ocProductImage->getImage())['basename'];
                    if ($baseName_productImage == $remoteFile['name']) {
                        $arquivoRemotoExisteNaBase = true;
                        break;
                    }
                }
            }
            if (!$arquivoRemotoExisteNaBase) {
                ftp_delete($ftpConn, $remoteFile['name']);
            }
        }

        // Apago do cachê para forçar atualização na próxima visita
        $ftpProductImagesCacheFolder = getenv('OC_FTP_PRODUCT_IMAGE_CACHE_FOLDER');
        $this->getFtpUtils()->recursiveDeleteDirFTP($ftpConn, $ftpProductImagesCacheFolder . '/' . $fornecedorFolder . '/' . $produtoFolder);

        if (!$ftpAberto) {
            ftp_close($ftpConn);
        }

        return $qtdeImagensAtualizadas;
    }

    /**
     * Salva todas as imagens dos produtos passados abrindo e fechando apenas uma vez a conexão FTP.
     * @param $produtos
     * @throws ORMException
     * @throws ViewException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveImagesBatch($produtos)
    {
        $ftpServer = getenv('OC_FTP_SERVER');
        $ftpUsername = getenv('OC_FTP_USERNAME');
        $ftpPassword = getenv('OC_FTP_PASSWORD');
        $ftpConn = ftp_connect($ftpServer);
        foreach ($produtos as $produto) {
            $this->saveImages($produto, $ftpConn);
        }
        ftp_close($ftpConn);
    }

    /**
     * Salva os filtros do produto.
     *
     * @param Produto $produto
     * @param OcProduct $ocProduct
     * @param null $filters
     * @throws \Exception
     */
    private function saveOcProductFilter(Produto $produto, OcProduct $ocProduct, $filters = null)
    {
        try {
            $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
            $ocProductId = $ocProduct->getProductId();// primeiro removo todos para depois inserir novamente
            $productFilters = $ocEntityManager->getRepository(OcProductFilter::class)->findBy(['productId' => $ocProductId]);
            foreach ($productFilters as $productFilter) {
                $ocEntityManager->remove($productFilter);
            }
            $ocEntityManager->flush();
            if (isset($filters) and count($filters) > 0) {
                // Se veio pelo form
                foreach ($filters as $filter) {
                    $ocProductFilter = new OcProductFilter();
                    $ocProductFilter->setProductId($ocProductId);
                    $ocProductFilter->setFilterId($filter);
                    $ocEntityManager->persist($ocProductFilter);
                }
                $ocEntityManager->flush();
            } else {
                // Se é o primeiro save
                // Percorre os tamanhos e verifica se precisa inserir o oc_filter para o oc_product
                $this->salvarGradeTamanhosComoFiltros($produto, $ocProduct);
                // Depois, salva a marca como um filtro
                $this->salvarMarcaComoFiltro($produto, $ocProduct);
            }
            $this->salvarFiltrosProdutoNosFiltrosDaCategoria($ocProduct);
            $this->salvarFiltrosComoAtributos($ocProduct);
            $ocEntityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('Erro - saveOcProductFilter()', 0, $e);
        }
    }

    /**
     * Salva os 'gradeTamanho' do produto como filtros.
     *
     * @param Produto $produto
     * @param OcProduct $ocProduct
     * @throws \Exception
     */
    private function salvarGradeTamanhosComoFiltros(Produto $produto, OcProduct $ocProduct)
    {
        try {
            $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
            $ocProductId = $ocProduct->getProductId();
            foreach ($produto->getSaldos() as $saldo) {
                if (!$saldo->getSelec()) continue; // somente para gradeTamanho selecionado.

                $tamanho = $saldo->getGradeTamanho()->getTamanho();
                // encontra o filtro correspondente ao gradeTamanho
                $ocFilterDescription = $ocEntityManager->getRepository(OcFilterDescription::class)->findOneBy(['name' => $tamanho]);

                // Verifica se o produto já possui este filtro
                $ocProductFilter = $ocEntityManager->getRepository(OcProductFilter::class)
                    ->findOneBy([
                        'filterId' => $ocFilterDescription->getFilterId(),
                        'productId' => $ocProductId
                    ]);

                // Se não, salva
                if (!$ocProductFilter) {
                    $ocProductFilter = new OcProductFilter();
                    $ocProductFilter->setProductId($ocProductId);
                    $ocProductFilter->setFilterId($ocFilterDescription->getFilterId());
                    $ocEntityManager->persist($ocProductFilter);
                    $ocEntityManager->flush();
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Erro ao salvarMarcaComoFiltro()', 0, $e);
        }
    }


    /**
     * Salva a marca do produto como um filtro.
     *
     * @param Produto $produto
     * @param OcProduct $ocProduct
     * @throws \Exception
     */
    private function salvarMarcaComoFiltro(Produto $produto, OcProduct $ocProduct)
    {
        try {
            $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
            $ocProductId = $ocProduct->getProductId();

            // Salvo a marca como um filtro
            $manufacturer = $ocEntityManager->getRepository(OcManufacturer::class)->find($ocProduct->getManufacturerId());
            // No caso de uniformes escolares, chamado o filtro de 'Escola' para não confundir os clientes da loja virtual
            $filtro = $this->getProdutoBusiness()->ehUniformeEscolar($produto) ? 'Escola' : 'Marca';
            $ocFilterGroupDescription = $ocEntityManager->getRepository(OcFilterGroupDescription::class)->findOneBy(['name' => $filtro]);
            if (!$ocFilterGroupDescription) {
                throw new \Exception('Faltando o filtro "' . $filtro . '"');
            }
            $filtroPorMarca = $ocEntityManager->getRepository(OcFilterDescription::class)
                ->findOneBy([
                    'name' => $manufacturer->getName(),
                    'filterGroupId' => $ocFilterGroupDescription->getFilterGroupId()]);
            if (!$filtroPorMarca) {
                $ocFilter = new OcFilter();
                $ocFilter->setFilterGroupId($ocFilterGroupDescription->getFilterGroupId());
                $ocFilter->setSortOrder(0);
                $ocEntityManager->persist($ocFilter);
                $ocEntityManager->flush();

                $filtroPorMarca = new OcFilterDescription();
                $filtroPorMarca->setFilterId($ocFilter->getFilterId());
                $filtroPorMarca->setFilterGroupId($ocFilterGroupDescription->getFilterGroupId());
                $filtroPorMarca->setLanguageId(2); // fixo na base
                $filtroPorMarca->setName($manufacturer->getName());
                $ocEntityManager->persist($filtroPorMarca);
                $ocEntityManager->flush();
            }

            // Como todos os filtros foram removidos do produto, apenas incluo
            $ocProductFilter = new OcProductFilter();
            $ocProductFilter->setProductId($ocProductId);
            $ocProductFilter->setFilterId($filtroPorMarca->getFilterId());
            $ocEntityManager->persist($ocProductFilter);
            $ocEntityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('Erro ao salvarMarcaComoFiltro()', 0, $e);
        }
    }

    /**
     * Salva os filtros do produto como filtros das categorias do produto.
     * É necessário para que os filtros sejam exibidos no menu esquerdo quando uma categoria é selecionada.
     *
     * @param OcProduct $ocProduct
     * @throws \Exception
     */
    private function salvarFiltrosProdutoNosFiltrosDaCategoria(OcProduct $ocProduct)
    {
        try {
            $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
            $ocProductId = $ocProduct->getProductId();
            $ocProductFilters = $ocEntityManager->getRepository(OcProductFilter::class)->findBy(['productId' => $ocProductId]);

            $ocProductCategories = $ocEntityManager->getRepository(OcProductToCategory::class)->findBy(['productId' => $ocProductId]);
            foreach ($ocProductCategories as $category) {

                if ($ocProductFilters) {
                    foreach ($ocProductFilters as $filter) {

                        $ocCategoryFilter = $ocEntityManager->getRepository(OcCategoryFilter::class)
                            ->findOneBy([
                                'categoryId' => $category->getCategoryId(),
                                'filterId' => $filter->getFilterId()
                            ]);
                        if (!$ocCategoryFilter) {
                            $ocCategoryFilter = new OcCategoryFilter();
                            $ocCategoryFilter->setCategoryId($category->getCategoryId());
                            $ocCategoryFilter->setFilterId($filter->getFilterId());
                            $ocEntityManager->persist($ocCategoryFilter);
                            $ocEntityManager->flush();
                        }
                    }
                }
            }
        } catch (ORMException $e) {
            throw new \Exception('Erro ao salvarFiltrosProdutoNosFiltrosDaCategoria()', 0, $e);
        }
    }

    /**
     * Salva os filtros do produto como atributos.
     * Para ficar bem especificado na página no produto.
     *
     * @param OcProduct $ocProduct
     * @throws \Exception
     */
    private function salvarFiltrosComoAtributos(OcProduct $ocProduct)
    {
        try {
            $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
            $ocProductId = $ocProduct->getProductId();
            $productFilters = $ocEntityManager->getRepository(OcProductFilter::class)->findBy(['productId' => $ocProductId]);
            foreach ($productFilters as $productFilter) {
                $ocFilterDescription = $ocEntityManager->getRepository(OcFilterDescription::class)->findOneBy(['filterId' => $productFilter->getFilterId()]);
                $ocFilterGroupDescription = $ocEntityManager->getRepository(OcFilterGroupDescription::class)->findOneBy(['filterGroupId' => $ocFilterDescription->getFilterGroupId()]);
                $ocAttributeDescription = $ocEntityManager->getRepository(OcAttributeDescription::class)->findOneBy(['name' => $ocFilterGroupDescription->getName()]);

                if (!$ocAttributeDescription) {
                    throw new \Exception('Atributo não encontrado para o filtro "' . $ocFilterGroupDescription->getName() . '". É necessário criá-lo');
                }
                $ocProductAttribute = $ocEntityManager->getRepository(OcProductAttribute::class)->findBy(['productId' => $ocProductId, 'attributeId' => $ocAttributeDescription->getAttributeId()]);
                if (!$ocProductAttribute) {
                    $ocProductAttribute = new OcProductAttribute();
                    $ocProductAttribute->setProductId($ocProductId);
                    $ocProductAttribute->setAttributeId($ocAttributeDescription->getAttributeId());
                    $ocProductAttribute->setLanguageId(2); // fixo na base
                    $ocProductAttribute->setText($ocFilterDescription->getName());
                    $ocEntityManager->persist($ocProductAttribute);
                    $ocEntityManager->flush();
                }
            }
        } catch (ORMException $e) {
            throw new \Exception('Erro ao salvarFiltrosComoAtributos()', 0, $e);
        }

    }


    /**
     * Remove da est_produto_oc_product os registros que tiverem sido deletados da oc_product.
     *
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function corrigirEstProdutoOcProduct()
    {
        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');

        $produtosNaLoja = $this->getDoctrine()->getRepository(ProdutoOcProduct::class)->findAll(WhereBuilder::buildOrderBy('productId DESC'));

        $qtdeTotal = count($produtosNaLoja);
        $qtdeRemovidos = 0;
        foreach ($produtosNaLoja as $prod) {
            $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($prod->getProductId());
            if (!$ocProduct) {
                $this->getDoctrine()->getEntityManager()->remove($prod);
                $prod->getProduto()->setNaLojaVirtual(false);
                $this->getDoctrine()->getEntityManager()->persist($prod->getProduto());
                $this->getDoctrine()->getEntityManager()->flush();
                $qtdeRemovidos++;
            }
        }
        return 'Removidos: ' . $qtdeRemovidos . '/' . $qtdeTotal;
    }


    /**
     * Arruma a descrição do ocProduct conforme as regras.
     *
     * @param Produto $produto
     * @throws ORMException
     * @throws ViewException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function corrigirOcNomeEDescricao(Produto $produto)
    {
        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
        $ocProduct = $this->getOcProductByProduto($produto);
        $ocProductDescription = $ocEntityManager->getRepository(OcProductDescription::class)->findOneBy(['productId' => $ocProduct->getProductId()]);

        $subdeptos['BERMUDA ESCOLAR ACTION'] = 'Bermuda';
        $subdeptos['BERMUDA ESCOLAR MALHA'] = 'Bermuda';
        $subdeptos['BLUSA MOLETON ADULTO'] = 'Blusa de Moletom';
        $subdeptos['BLUSA MOLETON INFANTIL'] = 'Blusa de Moletom';
        $subdeptos['CALCA   ESCOLAR ACTION'] = 'Calça';
        $subdeptos['CALCA   ESCOLAR MALHA'] = 'Calça';
        $subdeptos['CALCA ESCOLAR LEGGING'] = 'Legging';
        $subdeptos['CAMISA POLO'] = 'Camisa Pólo';
        $subdeptos['CAMISETA ESCOLAR MC PA'] = 'Camiseta Manga Curta';
        $subdeptos['CAMISETA ESCOLAR MC PV'] = 'Camiseta Manga Curta';
        $subdeptos['CAMISETA ESCOLAR ML PA'] = 'Camiseta Manga Longa';
        $subdeptos['CAMISETA ESCOLAR ML PV'] = 'Camiseta Manga Longa';
        $subdeptos['JAQUETA ESCOLAR ACTION'] = 'Jaqueta';
        $subdeptos['JAQUETA ESCOLAR MALHA'] = 'Jaqueta';

        // >>> MATERIAL
        $materiais['ACT'] = 'Action (Tactel)';
        $materiais['ACTION'] = 'Action (Tactel)';
        $materiais['DOUBLE'] = 'Double';
        $materiais['DRY'] = 'Dryfit';
        $materiais['MALHA'] = 'Malha Escolar';
        $materiais['MATEL'] = 'Matelada';
        $materiais['MLH'] = 'Malha Escolar';
        $materiais['PA'] = 'Polialgodão';
        $materiais['POLITEL'] = 'Action (Tactel)';
        $materiais['PV'] = 'Poliviscose';
        $materiais['SUPLEX'] = 'Suplex';
        $materiais['TACTEL'] = 'Action (Tactel)';

        // >>> CORES
        $cores['AZUL'] = 'Azul';
        $cores['ROSA'] = 'Rosa';
        $cores['AMA'] = 'Amarelo';
        $cores['AMAR'] = 'Amarelo';
        $cores['AMR'] = 'Amarelo';
        $cores['AZ'] = 'Azul';
        $cores['(AZ){1}(.)+(CLR){1}'] = 'Azul Claro';
        $cores['(AZ){1}(.)+(ROY){1}'] = 'Azul Royal';
        $cores['AZM'] = 'Azul Marinho';
        $cores['BCA'] = 'Branco';
        $cores['BORDO'] = 'Bordô';
        $cores['BRANCA'] = 'Branco';
        $cores['BRD'] = 'Bordô';
        $cores['LRJ'] = 'Laranja';
        $cores['MESCLA'] = 'Mescla';
        $cores['MSC'] = 'Mescla';
        $cores['PRETA'] = 'Preto';
        $cores['PT'] = 'Preto';
        $cores['PTA'] = 'Preto';
        $cores['PTA/VRD'] = 'Preto/Verde';
        $cores['ROYAL'] = 'Azul Royal';
        $cores['VDE'] = 'Verde';
        $cores['VERM'] = 'Vermelho';
        $cores['VM'] = 'Vermelho';
        $cores['VRD'] = 'Verde';
        $cores['VRM'] = 'Vermelho';

        $tamanhos = ['02', '04', '06', '08', '10', '12', '14', '16', 'P', 'M', 'G', 'XG'];

        // >>> MOLDE
        // $moldes['MC'] = 'Manga Curta';
        // $moldes['ML'] = 'Manga Longa';
        $moldes['BAS'] = 'Básico';
        $moldes['BAS.CAP.'] = 'Básico com Capuz';
        $moldes['BASICO'] = 'Básico';
        $moldes['CANG FEC'] = 'Canguru Fechado';
        $moldes['CG FC'] = 'Canguru Fechado';
        $moldes['CNG FC'] = 'Canguru Fechado';
        $moldes['CNG FEC'] = 'Canguru Fechado';
        $moldes['CNG FEC CAP'] = 'Canguru Fechado com Capuz';
        $moldes['CNG S/C'] = 'Canguru sem Capuz';
        $moldes['CP'] = 'Com Capuz';
        $moldes['CURTA'] = 'Curta';
        $moldes['LONGA'] = 'Longa';
        $moldes['FEM'] = 'Feminina';
        $moldes['MASC'] = 'Masculina';
        $moldes['REG'] = 'Regata';
        $moldes['S/CAP'] = 'Sem Capuz';

        $novaDescricao = $subdeptos[trim($produto->getSubdepto()->getNome())];

        $ocManufacturer = $ocEntityManager->getRepository(OcManufacturer::class)->find($ocProduct->getManufacturerId());

        $novaDescricao .= ' ' . mb_strtoupper($ocManufacturer->getName());


        foreach ($materiais as $key => $material) {
            if (strpos($produto->getDescricao(), ' ' . $key) !== FALSE) {
                $novaDescricao .= ' em ' . $material;
                break;
            }
        }

        foreach ($moldes as $key => $molde) {
            if (strpos($produto->getDescricao(), ' ' . $key) !== FALSE) {
                $novaDescricao .= ' ' . $molde;
                break;
            }
        }

        foreach ($cores as $key => $cor) {
            if (strpos($produto->getDescricao(), $key) !== FALSE) {
                $novaDescricao .= ' ' . $cor;
                break;
            }
        }

        foreach ($tamanhos as $tam) {
            if (preg_match('/^(.)+( ' . $tam . '){1}$/', trim($produto->getDescricao()))) {
                $novaDescricao .= ' Tam.' . $tam;
                break;
            }
        }

        $ocProductDescription->setName($novaDescricao);

        $tipo = null;
        if (strpos($novaDescricao, 'Action') !== FALSE) {
            $tipo = 'action';
        } else if (strpos($novaDescricao, 'Malha') !== FALSE) {
            $tipo = 'gaucha';
        } else if (strpos($novaDescricao, 'Moleto') !== FALSE) {
            $tipo = 'moletom';
        } else if (strpos($novaDescricao, 'Polialgodão') !== FALSE) {
            $tipo = 'pa';
        } else if (strpos($novaDescricao, 'Poliviscose') !== FALSE) {
            $tipo = 'pv';
        } else if (strpos($novaDescricao, 'Suplex') !== FALSE) {
            $tipo = 'suplex';
        }

        if ($tipo) {
            $codigoEscola = $produto->getFornecedor()->getCodigoEkt();

            $ch = curl_init("https://loja.casabonsucesso.com.br/image/catalog/descricoes/HTML_" . $tipo . ".php?codigo_escola=" . $codigoEscola . "&titulo=" . urlencode($novaDescricao));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $descricao = curl_exec($ch);
            curl_close($ch);
        } else {
            $descricao = $novaDescricao;
        }

        $ocProductDescription->setName($novaDescricao);
        $ocProductDescription->setDescription($descricao);
        $ocEntityManager->flush();


    }

    /**
     * @return mixed
     */
    public function getProdutoBusiness(): ProdutoBusiness
    {
        return $this->produtoBusiness;
    }

    /**
     * @required
     * @param mixed $produtoBusiness
     */
    public function setProdutoBusiness(ProdutoBusiness $produtoBusiness): void
    {
        $this->produtoBusiness = $produtoBusiness;
    }

    /**
     * @return mixed
     */
    public function getFtpUtils(): FTPUtils
    {
        return $this->ftpUtils;
    }

    /**
     * @required
     * @param mixed $ftpUtils
     */
    public function setFtpUtils(FTPUtils $ftpUtils): void
    {
        $this->ftpUtils = $ftpUtils;
    }


}