'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);


$(document).ready(function () {

    $('#tipoExtrato').on('select2:select', function () {
            let tipoExtrato = $('#tipoExtrato').val();
            $('#grupoRow, #carteiraExtratoRow, #carteiraDestinoRow, #gerarRow').hide();
            if (tipoExtrato.includes('GRUPO')) {
                $('#grupoRow').show();
            } else if (tipoExtrato.includes('DEBITO')) {
                $('#carteiraExtratoRow').show();
                $('#carteiraDestinoRow').show();
                $('#gerarRow').show();
            } else {
                $('#carteiraExtratoRow').show();
                $('#gerarRow').show();
            }

        }
    );

    $.getJSON(
        Routing.generate('fin_movimentacao_import_tiposExtratos'),
        function (results) {
            $("#tipoExtrato").select2({
                    data: results
                }
            );
            $('#tipoExtrato').trigger('select2:select');
        }
    );

    $('#grupo').on('select2:select', function () {
        $.ajax({
            url: Routing.generate('fin_grupoItem_select2json') + '/' + $('#grupo').val(),
            dataType: 'json',
            success: function (result) {
                result.unshift({"id": "-1", "text": "Selecione..."});
                $('#grupoItem').empty().trigger("change");
                $("#grupoItem").select2({
                    data: result,
                    width: '100%'
                });
            }
        });
    });


    $.getJSON(
        Routing.generate('fin_grupo_select2json'),
        function (results) {
            console.log(results);
            $("#grupo").select2({
                    data: results,
                    width: '100%'

                }
            );
            $('#grupo').trigger('select2:select');
        });

    $.getJSON(
        Routing.generate('fin_carteira_select2json'),
        function (results) {
            console.log(results);
            results.unshift({"id": "-1", "text": "Selecione..."});
            $("#carteiraExtrato").select2({
                    data: results,
                    width: '100%'
                }
            );
        });

    $.getJSON(
        Routing.generate('fin_carteira_select2json'),
        function (results) {
            console.log(results);
            results.unshift({"id": "-1", "text": "Selecione..."});
            $("#carteiraDestino").select2({
                    data: results,
                    width: '100%'
                }
            );
        });


});