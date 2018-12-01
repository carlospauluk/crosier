<?php

namespace App\Command\Estoque;

use App\Business\Estoque\OCBusiness;
use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\ProdutoOcProduct;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @package App\Command\Base
 */
class CorrigirSaldosOCCommand extends Command
{

    private $doctrine;

    private $ocBusiness;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('crosier:corrigirSaldosOCCommand');
    }

    /**
     * Cria os option e option_value na base do OC e vincula com as grades e tamanhos.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $produtosOcProducts = $this->doctrine->getRepository(ProdutoOcProduct::class)->findAll(WhereBuilder::buildOrderBy('productId'));
        foreach ($produtosOcProducts as $produtoOcProduct) {
            try {
                $output->writeln('Atualizando produto "' . $produtoOcProduct->getProduto()->getReduzido() . ' - ' . $produtoOcProduct->getProduto()->getDescricao() . '"');
                $this->getOcBusiness()->saveOcProduct($produtoOcProduct->getProduto());
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }
        }
        $output->writeln('OK!!!');
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
    public function getOcBusiness(): OCBusiness
    {
        return $this->ocBusiness;
    }

    /**
     * @required
     * @param OCBusiness $ocBusiness
     */
    public function setOcBusiness(OCBusiness $ocBusiness): void
    {
        $this->ocBusiness = $ocBusiness;
    }


}