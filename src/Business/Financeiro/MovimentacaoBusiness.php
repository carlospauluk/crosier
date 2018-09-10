<?php

namespace App\Business\Financeiro;

use App\Entity\Base\Endereco;
use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\CRM\ClienteEntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MovimentacaoBusiness
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function somarMovimentacoes($movs) {
        $total = 0.0;
        foreach ($movs as $m) {
            $total = $m->getCategoria()->getCodigoSuper() == 1 ? $total + $m->getValorTotal() : $total - $m->getValorTotal();
        }
        return $total;
    }
}