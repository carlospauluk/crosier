<?php

namespace App\Entity\Financeiro;

/**
 * Constantes para movimentacao.status.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
final class Status
{

    // É uma conta a pagar/receber
    const ABERTA = array(
        'label' => 'A pagar/receber',
        'sigla' => 'ABERTA',
        'icone' => 'fas fa-stopwatch'
    );

    // Em caso de cheque, quando já foi dado porém ainda não compensou // FIXME: meio inútil isso, não?
    const A_COMPENSAR = array(
        'label' => 'A compensar',
        'sigla' => 'A_COMPENSAR',
        'icone' => 'fas fa-stopwatch'
    );

    // Movimentações com previsão para realização
    const PREVISTA = array(
        'label' => 'Prevista',
        'sigla' => 'PREVISTA',
        'icone' => 'fas fa-stopwatch'
    );

    // Movimentação já realizada
    const REALIZADA = array(
        'label' => 'Realizada',
        'sigla' => 'REALIZADA',
        'icone' => 'fas fa-check-circle'
    );

    const ALL = array(
        Status::ABERTA,
        Status::A_COMPENSAR,
        Status::PREVISTA,
        Status::REALIZADA
    );


    public static function getChoices()
    {
        $arr = array();
        foreach (Status::ALL as $status) {
            $arr[$status['label']] = $status['sigla'];
        }
        return $arr;
    }

    public static function get($sigla)
    {
        foreach (Status::ALL as $st) {
            if ($st['sigla'] === $sigla) {
                return $st;
            }
        }
        return null;
    }


}

