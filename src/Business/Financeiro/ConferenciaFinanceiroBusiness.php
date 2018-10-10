<?php


namespace App\Business\Financeiro;

/**
 * Class ConferenciaFinanceiroBusiness.
 * Agrega as funcionalidades para o ConferenciaFinanceiroController.
 *
 * @package App\Business\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ConferenciaFinanceiroBusiness
{

    public function buildLists(\DateTime $dtIni, \DateTime $dtFim)
    {
        $listCaixas = $this->buildListCaixas($dtIni, $dtFim);
        $listCartoesDebito = $this->buildListCartoesDebito($dtIni, $dtFim);
        $listCartoesCredito = $this->buildListCartoesCredito($dtIni, $dtFim);
        $listGruposMoviment = $this->buildListGruposMoviment($dtIni, $dtFim);
        $listTransfsCarteiras = $this->buildListTransfsCarteiras($dtIni, $dtFim);

        $lists = [];
        $lists['caixas'] = $listCaixas;
        $lists['cartoesDebito'] = $listCartoesDebito;
        $lists['cartoesCredito'] = $listCartoesCredito;
        $lists['gruposMoviment'] = $listGruposMoviment;
        $lists['transfsCarteiras'] = $listTransfsCarteiras;

        return $lists;
    }


    private function buildListCaixas(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListCartoesDebito(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListCartoesCredito(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListGruposMoviment(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListTransfsCarteiras(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

}