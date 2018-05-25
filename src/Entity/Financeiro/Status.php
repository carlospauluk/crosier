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
        'icone' => 'waiting'
    );

    // Em caso de cheque, quando já foi dado porém ainda não compensou // FIXME: meio inútil isso, não?
    const A_COMPENSAR = array(
        'label' => 'A compensar',
        'sigla' => 'A_COMPENSAR',
        'icone' => 'waiting'
    );

    // Movimentações com previsão para realização
    const PREVISTA = array(
        'label' => 'Prevista',
        'sigla' => 'PREVISTA',
        'icone' => 'waiting'
    );

    // Movimentação já realizada
    const REALIZADA = array(
        'label' => 'Realizada',
        'sigla' => 'REALIZADA',
        'icone' => 'checked'
    );

    const ALL = array(
        Status::ABERTA,
        Status::A_COMPENSAR,
        Status::PREVISTA,
        Status::REALIZADA
    );

    
}

