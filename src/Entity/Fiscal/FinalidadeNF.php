<?php

namespace App\Entity\Fiscal;

class FinalidadeNF
{

    const NORMAL = array(
        'key' => 'NORMAL',
        'codigo' => 1,
        'label' => 'NF-e normal'
    );

    const COMPLEMENTAR = array(
        'key' => 'COMPLEMENTAR',
        'codigo' => 2,
        'label' => 'NF-e complementar'
    );

    const AJUSTE = array(
        'key' => 'AJUSTE',
        'codigo' => 3,
        'label' => 'NF-e de ajuste'
    );

    const DEVOLUCAO = array(
        'key' => 'DEVOLUCAO',
        'codigo' => 4,
        'label' => 'Devolução de mercadoria'
    );


    const ALL = array(
        FinalidadeNF::NORMAL,
        FinalidadeNF::COMPLEMENTAR,
        FinalidadeNF::AJUSTE,
        FinalidadeNF::DEVOLUCAO
    );


    public static function getChoices()
    {
        $arr = array();
        foreach (FinalidadeNF::ALL as $e) {
            $arr[$e['label']] = $e['codigo'];
        }
        return $arr;
    }

    public static function get($key)
    {
        foreach (FinalidadeNF::ALL as $e) {
            if ($e['key'] == $key) {
                return $e;
            }
        }
    }


}