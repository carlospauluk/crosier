<?php

namespace App\Command\Estoque;

use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Depto;
use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoOcProduct;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * export XDEBUG_CONFIG="idekey=session_name"
 *
 * @package App\Command\Base
 */
class EstoqueConferCommand extends Command
{

    private $doctrine;

    private $ocBusiness;


    protected function configure()
    {
        $this
            ->setName('crosier:estoqueConferCommand');
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


        echo "bla";





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

}