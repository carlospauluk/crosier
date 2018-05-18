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
    const A_PAGAR_RECEBER = array("A Pagar/Receber"); // Uma conta a pagar ou a receber
    const CHEQUE_PROPRIO = array("Cheque Próprio"); // Um pagamento feito com cheque
    const CHEQUE_TERCEIROS = array("Cheque de Terceiros"); // Um recebimento feito com cheque
    const GERAL = array("Geral"); // Uma movimentação onde é possível manipular todos os campos
    const REALIZADA = array("Realizada"); // Uma movimentação já realizada
    const TRANSF_CAIXA = array("Transferência de Entrada de Caixa"); // Uma transferência entre contas que na verdade é uma transferência/recolhimento de caixa (são feitas 3 movimentações)
    const TRANSF_PROPRIA = array("Transferência entre Carteiras"); // Uma transferência entre carteiras próprias (são feitas 2 movimentações)
    const MOVIMENTACAO_AGRUPADA = array("Movimentação Agrupada");
    const ESTORNO_CORRECAO = array("Estorno/Correção"); // 2 movimentações em cadeia, para estornar um valor e lançar na categoria correta
    const PARCELA = array("Parcela"); // Utilizado para adicionar uma parcela a um parcelamento já existente
    const PAGTO = array("Pagamento"); // Uma transferência entre carteiras próprias (são feitas 2 movimentações)
}

