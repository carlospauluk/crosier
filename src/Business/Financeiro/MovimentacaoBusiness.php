<?php

namespace App\Business\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MovimentacaoBusiness
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function somarMovimentacoes($movs)
    {
        $total = 0.0;
        foreach ($movs as $m) {
            $total = $m->getCategoria()->getCodigoSuper() == 1 ? $total + $m->getValorTotal() : $total - $m->getValorTotal();
        }
        return $total;
    }

    /**
     * @param Movimentacao $movimentacao
     */
    public function mergeAll(Movimentacao $movimentacao)
    {
        $em = $this->doctrine->getEntityManager();
        if ($movimentacao->getCategoria() and $movimentacao->getCategoria()->getId()) {
            $movimentacao->setCategoria($em->merge($movimentacao->getCategoria()));
        }
        if ($movimentacao->getCarteira() and $movimentacao->getCarteira()->getId()) {
            $movimentacao->setCarteira($em->merge($movimentacao->getCarteira()));
        }
        if ($movimentacao->getCarteiraDestino() and $movimentacao->getCarteiraDestino()->getId()) {
            $movimentacao->setCarteiraDestino($em->merge($movimentacao->getCarteiraDestino()));
        }
        if ($movimentacao->getCentroCusto() and $movimentacao->getCentroCusto()->getId()) {
            $movimentacao->setCentroCusto($em->merge($movimentacao->getCentroCusto()));
        }
        if ($movimentacao->getModo() and $movimentacao->getModo()->getId()) {
            $movimentacao->setModo($em->merge($movimentacao->getModo()));
        }
        if ($movimentacao->getGrupoItem() and $movimentacao->getCarteira()->getId()) {
            $movimentacao->setGrupoItem($em->merge($movimentacao->getGrupoItem()));
        }
        if ($movimentacao->getOperadoraCartao() and $movimentacao->getOperadoraCartao()->getId()) {
            $movimentacao->setOperadoraCartao($em->merge($movimentacao->getOperadoraCartao()));
        }
        if ($movimentacao->getBandeiraCartao() and $movimentacao->getBandeiraCartao()->getId()) {
            $movimentacao->setBandeiraCartao($em->merge($movimentacao->getBandeiraCartao()));
        }
        if ($movimentacao->getPessoa() and $movimentacao->getPessoa()->getId()) {
            $movimentacao->setPessoa($em->merge($movimentacao->getPessoa()));
        }
        if ($movimentacao->getCadeia() and $movimentacao->getCadeia()->getId()) {
            $movimentacao->setCadeia($em->merge($movimentacao->getCadeia()));
        }
        if ($movimentacao->getParcelamento() and $movimentacao->getParcelamento()->getId()) {
            $movimentacao->setParcelamento($em->merge($movimentacao->getParcelamento()));
        }
        if ($movimentacao->getDocumentoBanco() and $movimentacao->getDocumentoBanco()->getId()) {
            $movimentacao->setDocumentoBanco($em->merge($movimentacao->getDocumentoBanco()));
        }
        if ($movimentacao->getChequeBanco() and $movimentacao->getChequeBanco()->getId()) {
            $movimentacao->setChequeBanco($em->merge($movimentacao->getChequeBanco()));
        }
    }
}