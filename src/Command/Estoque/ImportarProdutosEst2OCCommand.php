<?php

namespace App\Command\Estoque;

use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Depto;
use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\ProdutoOcProduct;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * export XDEBUG_CONFIG="idekey=session_name"
 *
 * @package App\Command\Base
 */
class ImportarProdutosEst2OCCommand extends Command
{

    private $doctrine;

    private $produtoBusiness;

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


    protected function configure()
    {
        $this
            ->setName('crosier:importarProdutosEst2OCCommand');
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
        //Executar com debug para verificar se não vai dar problema.
        $ocEntityManager = $this->doctrine->getEntityManager('oc');
        $em = $this->doctrine->getEntityManager();


            $deptoUniformes = $em->getRepository(Depto::class)->find(1);

            $ql = "SELECT p " .
                "FROM App\Entity\Estoque\Produto p, " .
                "App\Entity\Estoque\Fornecedor f " .
                "WHERE p.fornecedor = f AND p.atual = TRUE AND f.codigoEkt = :codigoEkt AND p.subdepto IN (SELECT s FROM App\Entity\Estoque\Subdepto s WHERE s.depto = :depto)";
            $qry = $em->createQuery($ql);
            $qry->setParameter('depto', $deptoUniformes);
            $qry->setParameter('codigoEkt', 573);

            $prods = $qry->getResult();
            foreach ($prods as $prod) {

                $produtoOcProduct = $em->getRepository(ProdutoOcProduct::class)->findBy(['produto' => $prod]);
                if (!$produtoOcProduct) {

                    $this->getProdutoBusiness()->saveOcProduct($prod, null);
//                    $em->persist($prod);
                    $prod->setNaLojaVirtual(true);
                    $em->flush();

                }

            }




    }

}