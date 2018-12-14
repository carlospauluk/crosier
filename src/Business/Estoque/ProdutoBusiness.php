<?php

namespace App\Business\Estoque;

use App\Business\BaseBusiness;
use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\Produto;
use App\EntityOC\OcProductDescription;
use App\Exception\ViewException;

class ProdutoBusiness extends BaseBusiness
{

    private $ocBusiness;

    public function ehUniformeEscolar(Produto $produto)
    {
        return $produto and $produto->getSubdepto() and $produto->getSubdepto()->getDepto() and $produto->getSubdepto()->getDepto()->getCodigo() == 1;
    }

    public function consultaPrecosGerarMsg($fornecedorId, $tamanho)
    {
        $fornecedor = $this->getDoctrine()->getRepository(Fornecedor::class)->find($fornecedorId);
        $msg = "";
        $ocEntityManager = $this->getDoctrine()->getEntityManager('oc');
        $produtos = $this->getDoctrine()->getRepository(Produto::class)->findBy(['fornecedor' => $fornecedor, 'atual' => true], ['descricao' => 'ASC']);


        $msg .= "Olá!!!!\n\n";
        $msg .= "O preço A PRAZO é para pagamento em até 6x sem juros nos cartões ou em nosso crediário!!\n";
        $msg .= "Já o preço a vista é com 10% de desconto, para pagamentos em dinheiro, no cartão de débito ou no crédito em 1x!!\n\n";



        foreach ($produtos as $produto) {

            if (!$produto->getSaldos()) {
                throw new ViewException('Produto sem saldos (' . $produto->getDescricao() . ')');
            }

            $ocProduct = $this->getOcBusiness()->getOcProductByProduto($produto);
            if ($ocProduct) {
                $ocProductDescription = $ocEntityManager->getRepository(OcProductDescription::class)->findOneBy(['productId' => $ocProduct->getProductId()]);
                $descricao = $ocProductDescription->getName();
            } else {
                $descricao = $produto->getDescricao();
            }

            foreach ($produto->getSaldos() as $saldo) {
                if (!$saldo->getSelec()) continue;
                if ($saldo->getGradeTamanho()->getTamanho() == $tamanho) {
                    $msg .= $descricao;
                    $msg .= " ..... A PRAZO: " . number_format($produto->getPrecoAtual()->getPrecoPrazo(), 2, ',', '.');
                    $msg .= " ..... A VISTA: " . number_format($produto->getPrecoAtual()->getPrecoVista(), 2, ',', '.');
                    $msg .= "\n";
                }
            }

        }

        $msg .= "\n\n\nNosso endereço: Av Dom Pedro II, 337, na Nova Rússia... em frente ao Shopping Total!\n";

        return $msg;
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
     * @param mixed $ocBusiness
     */
    public function setOcBusiness(OCBusiness $ocBusiness): void
    {
        $this->ocBusiness = $ocBusiness;
    }


}