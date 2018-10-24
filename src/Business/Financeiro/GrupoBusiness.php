<?php

namespace App\Business\Financeiro;

use App\Entity\Financeiro\Grupo;
use App\Entity\Financeiro\GrupoItem;
use App\EntityHandler\Financeiro\GrupoItemEntityHandler;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GrupoBusiness
{
    private $grupoItemEntityHandler;

    public function __construct(GrupoItemEntityHandler $grupoItemEntityHandler)
    {
        $this->grupoItemEntityHandler = $grupoItemEntityHandler;
    }


    /**
     * Gera um novo próximo item de grupo de movimentação.
     *
     * @param Grupo $pai
     * @return GrupoItem
     * @throws \Exception
     */
    public function gerarNovo(Grupo $pai)
    {
        try {
            $this->grupoItemEntityHandler->getEntityManager()->beginTransaction();

            $ultimo = $this->grupoItemEntityHandler->getEntityManager()->getRepository(GrupoItem::class)->findOneBy(['pai' => $pai], ['dtVencto' => 'DESC']);

            $novo = new GrupoItem();
            $novo->setPai($pai);
            $novo->setAnterior($ultimo);

            $proxDtVencto = clone $ultimo->getDtVencto();
            $proxDtVencto = $proxDtVencto->setDate($proxDtVencto->format('Y'), $proxDtVencto->format('m') + 1, $proxDtVencto->format('d'));
            $novo->setDtVencto($proxDtVencto);
            $novo->setCarteiraPagante($ultimo->getCarteiraPagante());
            $novo->setDescricao($pai->getDescricao() . ' - ' . $proxDtVencto->format('d/m/Y'));
            $novo->setFechado(false);
            $novo->setValorInformado(0.0);

            $this->grupoItemEntityHandler->save($novo);

            $ultimo->setProximo($novo);

            $this->grupoItemEntityHandler->save($ultimo);

            $this->grupoItemEntityHandler->getEntityManager()->commit();
            return $novo;
        } catch (\Exception $e) {
            $this->grupoItemEntityHandler->getEntityManager()->rollback();
            $erro = "Erro ao gerar novo item";
            throw new \Exception($erro, null, $e);
        }


    }

}