<?php

namespace App\Entity\Fiscal;

class TipoNotaFiscal
{

    const NFE = array(
        'codigo' => 55,
        'label' => 'Nota Fiscal'
    );

    const NFCE = array(
        'codigo' => 65,
        'label' => 'Nota Fiscal Consumidor'
    );

    const ALL = array(
        'NFE' => TipoNotaFiscal::NFE,
        'NFCE' => TipoNotaFiscal::NFCE
    );


    public static function getChoices()
    {
        $arr = array();
        foreach (TipoNotaFiscal::ALL as $status) {
            $arr[$status['label']] = $status['codigo'];
        }
        return $arr;
    }

    public static function get($key)
    {
        $all = TipoNotaFiscal::ALL;
        return $all[$key];
    }

}