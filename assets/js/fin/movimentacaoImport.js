'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);


$(document).ready(function () {

    let $grupoRow = $('#grupoRow');
    let $carteiraExtratoRow = $('#carteiraExtratoRow');
    let $carteiraExtrato = $('#carteiraExtrato');
    let $carteiraDestinoRow = $('#carteiraDestinoRow');
    let $carteiraDestino = $('#carteiraDestino');
    let $gerarRow = $('#gerarRow');
    let $tipoExtrato = $('#tipoExtrato');
    let $grupoItem = $('#grupoItem');
    let $grupo = $('#grupo');


    /**
     * Tratamento de regras para show e hide dos campos.
     */
    $tipoExtrato.on('select2:select', function () {
            let tipoExtrato = $tipoExtrato.val();
            $grupoRow.hide().attr('required', false);
            $carteiraExtratoRow.hide().attr('required', false);
            $carteiraDestinoRow.hide().attr('required', false);
            $gerarRow.hide().attr('required', false);

            $('#grupoRow, #carteiraExtratoRow, #carteiraDestinoRow, #gerarRow').attr('required', false);
            if (tipoExtrato.includes('GRUPO')) {
                $grupoRow.show();
                $grupoRow.attr('required', true);
            } else if (tipoExtrato.includes('DEBITO')) {
                $carteiraExtratoRow.show();
                $carteiraDestinoRow.show();
                $carteiraExtratoRow.attr('required', true);
                $carteiraDestinoRow.attr('required', true);
                $('#gerarRow').show();
            } else {
                $carteiraExtratoRow.show();
                $carteiraExtratoRow.attr('required', true);
                $('#gerarRow').show();
            }
        }
    );

    /**
     * Montagem dos select2 para #tipoExtrato.
     */
    $.getJSON(
        Routing.generate('fin_movimentacao_import_tiposExtratos'),
        function (results) {
            $("#tipoExtrato").select2({
                    data: results
                }
            );
            $tipoExtrato.val($tipoExtrato.data('val')).trigger('change').trigger('select2:select');
        }
    );

    /**
     * Handler para evento no select2 #grupo
     */
    $grupo.on('select2:select', function () {
        $.ajax({
            url: Routing.generate('fin_grupoItem_select2json') + '/' + $grupo.val(),
            dataType: 'json',
            success: function (result) {
                result.unshift({"id": "", "text": "Selecione..."});
                $grupoItem.empty().trigger("change");
                $("#grupoItem").select2({
                    data: result,
                    width: '100%'
                });
                if ($grupoItem.data('val')) {
                    $grupoItem.val($grupoItem.data('val')).trigger('change').trigger('select2:select');
                }
            }
        });
    });


    /**
     * Montagem dos valores do select2 para #grupo.
     */
    $.getJSON(
        Routing.generate('fin_grupo_select2json'),
        function (results) {
            results.unshift({"id": "", "text": "Selecione..."});
            $("#grupo").select2({
                    data: results,
                    width: '100%'

                }
            );
            if ($grupo.data('val')) {
                $grupo.val($grupo.data('val')).trigger('change').trigger('select2:select');
            }
        });

    /**
     * Montagem dos valores do select2 para #carteiraExtrato.
     */
    $.getJSON(
        Routing.generate('fin_carteira_select2json'),
        function (results) {
            results.unshift({"id": "", "text": "Selecione..."});
            $carteiraExtrato.select2({
                    data: results,
                    width: '100%'
                }
            );
            if ($carteiraExtrato.data('val')) {
                $carteiraExtrato.val($carteiraExtrato.data('val')).trigger('change').trigger('select2:select');
            }
        });

    /**
     * Montagem dos valores do select2 para #carteiraDestino.
     */
    $.getJSON(
        Routing.generate('fin_carteira_select2json'),
        function (results) {
            results.unshift({"id": "", "text": "Selecione..."});
            $carteiraDestino.select2({
                    data: results,
                    width: '100%'
                }
            );
            if ($carteiraDestino.data('val')) {
                $carteiraDestino.val($carteiraDestino.data('val')).trigger('change').trigger('select2:select');
            }
        });


});