<?php

namespace App\Command\Estoque;

use App\Business\Estoque\OCBusiness;
use App\Entity\Estoque\ProdutoOcProduct;
use App\EntityOC\OcProduct;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * export XDEBUG_CONFIG="idekey=session_name"
 *
 * @package App\Command\Base
 */
class ImportarProdutosEst2OCCommand extends Command
{

    private $doctrine;

    private $ocBusiness;


    protected function configure()
    {
        $this
            ->setName('crosier:atualizarPrecosOcProducts');
        $this
            ->addArgument('doWhat', InputArgument::REQUIRED, 'O que você quer fazer?');
    }

    /**
     * Cria os option e option_value na base do OC e vincula com as grades e tamanhos.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($input->getArgument('doWhat') == 'corrigirPrecosOcProducts') {
            $this->corrigirPrecos($output);
        } else if ($input->getArgument('doWhat') == 'corrigirProdutoOcProduct') {
            $this->corrigirProdutoOcProduct($output);
        }
    }

    private function corrigirPrecos(OutputInterface $output)
    {
        //Executar com debug para verificar se não vai dar problema.
        $ocEntityManager = $this->doctrine->getEntityManager('oc');

        $produtosOcProducts = $this->getDoctrine()->getRepository(ProdutoOcProduct::class)->findAll(WhereBuilder::buildOrderBy('productId'));

        $qtdeProdutosCorrigidos = 0;
        foreach ($produtosOcProducts as $produtoOcProduct) {
            $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($produtoOcProduct->getProductId());
            if ($ocProduct) {
                if ($produtoOcProduct->getProduto()->getPrecoAtual()->getPrecoPrazo() != $ocProduct->getPrice()) {
                    $output->writeln('--------------------------------------------');
                    $output->writeln('Produto: ' . $produtoOcProduct->getProduto()->getId() . ' - ' . $produtoOcProduct->getProduto()->getDescricao());
                    $output->writeln('Preço na loja: ' . $ocProduct->getPrice() . ' . Preço no Estoque: ' . $produtoOcProduct->getProduto()->getPrecoAtual()->getPrecoPrazo());
                    $output->writeln('Atualizando...');
                    $ocProduct->setPrice($produtoOcProduct->getProduto()->getPrecoAtual()->getPrecoPrazo());
                    $output->writeln('Ok.');
                    $qtdeProdutosCorrigidos++;
                }
            }
        }
        $ocEntityManager->flush();
        $output->writeln('--------------------------------------------');
        $output->writeln('--------------------------------------------');
        $output->writeln('--------------------------------------------');
        $output->writeln('Total de produtos corrigidos: ' . $qtdeProdutosCorrigidos);
    }

    private function corrigirProdutoOcProduct(OutputInterface $output)
    {
        $ocEntityManager = $this->doctrine->getEntityManager('oc');

        $produtosOcProducts = $this->getDoctrine()->getRepository(ProdutoOcProduct::class)->findAll(WhereBuilder::buildOrderBy('productId'));

        $qtdeProdutosCorrigidos = 0;
        foreach ($produtosOcProducts as $produtoOcProduct) {
            $ocProduct = $ocEntityManager->getRepository(OcProduct::class)->find($produtoOcProduct->getProductId());
            if (!$ocProduct) {
                $this->doctrine->getManager()->remove($produtoOcProduct);
                $qtdeProdutosCorrigidos++;
            }
        }
        $this->doctrine->getManager()->flush();
        $output->writeln('--------------------------------------------');
        $output->writeln('--------------------------------------------');
        $output->writeln('--------------------------------------------');
        $output->writeln('Total de produtos corrigidos: ' . $qtdeProdutosCorrigidos);
    }


    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        parent::__construct();
    }

    /**
     * @return RegistryInterface
     */
    public function getDoctrine(): RegistryInterface
    {
        return $this->doctrine;
    }

    /**
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getOcBusiness(): OcBusiness
    {
        return $this->ocBusiness;
    }

    /**
     * @required
     * @param mixed $ocBusiness
     */
    public function setOcBusiness(OcBusiness $ocBusiness): void
    {
        $this->ocBusiness = $ocBusiness;
    }


}