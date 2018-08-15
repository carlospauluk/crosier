<?php

namespace App\Entity\Fiscal;

/**
 * Segundo o Manual_Integracao_Contribuinte_4.01-NT2009.006 (3).pdf
 *
 * @author Carlos Eduardo Pauluk
 *
 */
final class ModalidadeFrete
{
    const SEM_FRETE = array(
        'codigo' => 9,
        'label' => 'Sem frete'
    );

    const EMITENTE = array(
        'codigo' => 0,
        'label' => 'Por conta do emitente'
    );

    const DESTINATARIO = array(
        'codigo' => 1,
        'label' => 'Por conta do destinatário/remetente'
    );

    const TERCEIROS = array(
        'codigo' => 2,
        'label' => 'Por conta de terceiros'
    );

    const ALL = array(
        'SEM_FRETE' => ModalidadeFrete::SEM_FRETE,
        'EMITENTE' => ModalidadeFrete::EMITENTE,
        'DESTINATARIO' => ModalidadeFrete::DESTINATARIO,
        'TERCEIROS' => ModalidadeFrete::TERCEIROS
    );

    public static function getChoices() {
        $arr = array();
        foreach (ModalidadeFrete::ALL as $e) {
            $arr[$e['label']] = $e['codigo'];
        }
        return $arr;
    }

    public static function get($key) {
        $all = ModalidadeFrete::ALL;
        return $all[$key];
    }
}