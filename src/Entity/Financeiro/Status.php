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
    const ABERTA = array("A pagar/receber", "A/PR", "waiting"); 
    
    // Em caso de cheque, quando já foi dado porém ainda não compensou // FIXME: meio inútil isso, não?
    const A_COMPENSAR = array("A compensar", "A/CO", "waiting"); 
    
    // Movimentações com previsão para realização
    const PREVISTA = array("Prevista", "PREV", "waiting");
    
    const REALIZADA = array("Realizada", "REAL", "checked"); 
}

