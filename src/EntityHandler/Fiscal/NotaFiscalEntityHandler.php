<?php

namespace App\EntityHandler\Fiscal;

use App\Business\Fiscal\NotaFiscalBusiness;
use App\Entity\Fiscal\NotaFiscal;
use App\EntityHandler\EntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotaFiscalEntityHandler extends EntityHandler
{

    private $notaFiscalBusiness;


    public function __construct(RegistryInterface $doctrine)
    {
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * @required
     * @param NotaFiscalBusiness $notaFiscalBusiness
     */
    public function setNotaFiscalBusiness(NotaFiscalBusiness $notaFiscalBusiness)
    {
        $this->notaFiscalBusiness = $notaFiscalBusiness;
    }

    public function getEntityClass()
    {
        return NotaFiscal::class;
    }

    public function beforePersist($notaFiscal)
    {
        $this->notaFiscalBusiness->calcularTotais($notaFiscal);
    }

    public function beforeClone($newE)
    {
        $newE->setUuid(null);
        $newE->setNumero(null);
        $newE->setCartaCorrecao(null);
        $newE->setRandFaturam(null);
        $newE->setCartaCorrecaoSeq(null);
        $newE->setChaveAcesso(null);
        $newE->setDtEmissao(new \DateTime());
        $newE->setDtSaiEnt(null);
        $newE->setSpartacusIdNota(null);
        $newE->setSpartacusMensretorno(null);
        $newE->setSpartacusMensretornoReceita(null);
        $newE->setSpartacusStatus(null);
        $newE->setSpartacusStatusReceita(null);
        $newE->setDtSpartacusStatus(null);
        $newE->setCnf(null);
        $newE->setMotivoCancelamento(null);
        $newE->setProtocoloAutorizacao(null);
        $newE->setXmlNota(null);

        if ($newE->getItens() and $newE->getItens()->count() > 0) {
            $oldItens = clone $newE->getItens();
            $newE->getItens()->clear();
            foreach ($oldItens as $oldItem) {
                $newItem = clone $oldItem;
                $newItem->setId(null);
                $newItem->setInserted(null);
                $newItem->setUserInserted(null);
                $newItem->setNotaFiscal($newE);
                $newE->getItens()->add($newItem);
            }
        }

        if ($newE->getHistoricos()) {
            $newE->getHistoricos()->clear();
        }

    }
}