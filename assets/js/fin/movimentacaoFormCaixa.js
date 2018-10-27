'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Moment from 'moment';


Routing.setRoutingData(routes)


$(document).ready(function () {


    function checkTipoLancto() {
        let tipoLancto = $('#movimentacao_tipoLancto').val();
        console.log(tipoLancto);


        let campos_TRANSF_PROPRIA = ['id', 'tipoLancto', 'status', 'carteira', 'carteiraDestino', 'modo', 'dtMoviment', 'descricao', 'obs', 'valor'];

        let campos_GERAL = [
            'id',
            'tipoLancto',
            'status',
            'carteira',
            'modo',
            'centroCusto',
            'dtMoviment',
            'dtVencto',
            'dtVenctoEfetiva',
            'dtPagto',
            'pessoa',
            'descricao',
            'documentoBanco',
            'documentoNum',
            'obs',
            'valor',
            'descontos',
            'acrescimos',
            'valorTotal',
            'chequeBanco',
            'chequeAgenda',
            'chequeConta',
            'chequeNumCheque',
            'chequeNumCheque',
        ];


        if (tipoLancto === 'TRANSF_PROPRIA') {

        }
    }

    checkTipoLancto();


    /**
     *
     */
    function loadCarteirasDestino() {
        $.getJSON(
            Routing.generate('fin_carteira_select2json'),
            function (results) {
                results.unshift({"id": "", "text": "Selecione..."});

                for (let i = results.length - 1; i >= 0; i--) {
                    if (results[i].id === $('#movimentacao_carteira').val()) {
                        results.splice(i, 1);
                        break;
                    }
                }

                $('#movimentacao_carteiraDestino').empty().trigger("change");
                $("#movimentacao_carteiraDestino").select2({
                        data: results,
                        width: '100%'
                    }
                );
                // Se veio o valor do PHP...
                if ($('#movimentacao_carteiraDestino').data('val')) {
                    $('#movimentacao_carteiraDestino').val($('#movimentacao_carteiraDestino').data('val')).trigger('change').trigger('select2:select');
                }
            });
    }


    $('#movimentacao_carteira').on('select2:select', function (e) {
        let tipoLancto = $('#movimentacao_tipoLancto').val();
        if (tipoLancto == 'TRANSF_PROPRIA') {
            loadCarteirasDestino();
        }
    });


    $("#movimentacao_modo").select2();

    $('#movimentacao_modo').on('select2:select', function () {
        checkModo();
    });

    checkModo();


    function checkModo() {
        let modo = $('#movimentacao_modo').select2('data')[0].text;
        console.log(modo);
        if (modo.includes('CARTÃO')) {
            $('.CAMPOS_CARTAO').parent().parent().collapse('show');
        } else {
            $('.CAMPOS_CARTAO').parent().parent().collapse('hide');
        }
    }

    $("#movimentacao_modo").select2();

    $('#movimentacao_modo').on('select2:select', function () {
        checkModo();
    });

    checkModo();


    $("#movimentacao_categoria").select2();

    $.getJSON(
        Routing.generate('fin_carteira_select2json') + '/?caixa=true',

        function (results) {
            let v = $('#movimentacao_carteira').val();
            $('#movimentacao_carteira').empty().trigger("change");
            $("#movimentacao_carteira").select2({
                    data: results,
                    width: '100%'
                }
            );
            // Se veio o valor do PHP...
            if (v) {
                $('#movimentacao_carteira').val(v).trigger('change').trigger('select2:select');
            }
        });


    $("#movimentacao_dtMoviment").focus(function () {
        if ($("#movimentacao_dtMoviment").val() == '') {
            $("#movimentacao_dtMoviment").val(Moment().format('DD/MM/YYYY'));
        }
    });


});
