<?php

namespace App\Entity\Financeiro;

/**
 * Constantes para movimentacao.tipoLancto.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
final class TipoLancto
{

    // Uma conta a pagar ou a receber
    const A_PAGAR_RECEBER = array(
        "label" => "A Pagar/Receber",
        "sigla" => "A_PAGAR_RECEBER"
    );

    // Um pagamento feito com cheque
    const CHEQUE_PROPRIO = array(
        "label" => "Cheque Próprio",
        "sigla" => "CHEQUE_PROPRIO");

    // Um recebimento feito com cheque
    const CHEQUE_TERCEIROS = array(
        "label" => "Cheque de Terceiros",
        "sigla" => "CHEQUE_TERCEIROS");

    // Uma movimentação onde é possível manipular todos os campos
    const GERAL = array(
        "label" => "Geral",
        "sigla" => "GERAL");

    // Uma movimentação já realizada
    const REALIZADA = array(
        "label" => "Realizada",
        "sigla" => "REALIZADA");

    // Uma transferência entre contas que na verdade é uma transferência/recolhimento de caixa (são feitas 3 movimentações)
    const TRANSF_CAIXA = array(
        "label" => "Transferência de Entrada de Caixa",
        "sigla" => "TRANSF_CAIXA");

    // Uma transferência entre carteiras próprias (são feitas 2 movimentações)
    const TRANSF_PROPRIA = array(
        "label" => "Transferência entre Carteiras",
        "sigla" => "TRANSF_PROPRIA");

    const MOVIMENTACAO_AGRUPADA = array(
        "label" => "Movimentação Agrupada",
        "sigla" => "MOVIMENTACAO_AGRUPADA");

    // 2 movimentações em cadeia, para estornar um valor e lançar na categoria correta
    const ESTORNO_CORRECAO = array(
        "label" => "Estorno/Correção",
        "sigla" => "ESTORNO_CORRECAO");

    // Utilizado para adicionar uma parcela a um parcelamento já existente
    const PARCELA = array(
        "label" => "Parcela",
        "sigla" => "PARCELA");

    // Uma transferência entre carteiras próprias (são feitas 2 movimentações)
    const PAGTO = array(
        "label" => "Pagamento",
        "sigla" => "PAGTO");


    const ALL = array(
        TipoLancto::A_PAGAR_RECEBER,
        TipoLancto::CHEQUE_PROPRIO,
        TipoLancto::CHEQUE_TERCEIROS,
        TipoLancto::ESTORNO_CORRECAO,
        TipoLancto::GERAL,
        TipoLancto::MOVIMENTACAO_AGRUPADA,
        TipoLancto::PAGTO,
        TipoLancto::PARCELA,
        TipoLancto::REALIZADA,
        TipoLancto::TRANSF_CAIXA,
        TipoLancto::TRANSF_PROPRIA
    );


    public static function getChoices()
    {
        $arr = array();
        foreach (Status::ALL as $status) {
            $arr[$status['label']] = $status['sigla'];
        }
        return $arr;
    }

}

