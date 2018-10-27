'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Moment from 'moment';


Routing.setRoutingData(routes);


$(document).ready(function () {

    // ----------------------  TIPO_LANCTO

    let $movimentacao_id = $('#movimentacao_id');

    $.getJSON(
        Routing.generate('fin_movimentacao_getTiposLanctos') + ($movimentacao_id.val() ? $movimentacao_id.val() : ''),
        function (results) {
            let $tipoLancto = $('#movimentacao_tipoLancto');

            results = $.map(results.tiposLanctos, function(o,i) {
                return { id: i, text: o.title, route: o.route };
            })

            $tipoLancto.empty().trigger("change");
            $tipoLancto.select2({
                    data: results,
                    width: '100%'
                }
            );
            // Se veio o valor do PHP...
            if ($tipoLancto.data('val')) {
                $tipoLancto.val($tipoLancto.data('val')).trigger('change').trigger('select2:select');
            }
        });


    $("#movimentacao_carteira").select2();

    $("#movimentacao_modo").select2();

    $("#movimentacao_categoria").select2();

    $("#movimentacao_centroCusto").select2();

    $("#movimentacao_documentoBanco").select2();

    $("#movimentacao_chequeBanco").select2();


    $("#movimentacao_pessoa").select2({
        ajax: {
            delay: 250,
            url: function (params) {
                console.log(params);
                return Routing.generate('bse_pessoa_findByNome') + '/' + params.term;
            },
            dataType: 'json',
            processResults: function (data, params) {
                var dataNew = $.map(data.results, function (obj) {
                    obj.text = obj.nome; // replace name with the property used for the text
                    return obj;
                });
                return {results: dataNew};
            },
            cache: true
        },
        minimumInputLength: 1
    });


    function resValorTotal() {
        var valor = $("#movimentacao_valor").maskMoney('unmasked')[0];
        var descontos = $("#movimentacao_descontos").maskMoney('unmasked')[0];
        var acrescimos = $("#movimentacao_acrescimos").maskMoney('unmasked')[0];
        var valorTotal = (valor - descontos + acrescimos).toFixed(2);
        $("#movimentacao_valor_total").val(valorTotal).maskMoney('mask');
    }

    $("#movimentacao_valor").blur(function () {
        resValorTotal()
    });
    $("#movimentacao_descontos").blur(function () {
        resValorTotal()
    });
    $("#movimentacao_acrescimos").blur(function () {
        resValorTotal()
    });


    $("#movimentacao_dtMoviment").focus(function () {
        if ($("#movimentacao_dtMoviment").val() == '') {
            $("#movimentacao_dtMoviment").val(Moment().format('DD/MM/YYYY'));
        }
    });

    let dtVenctoS = null;
    $("#movimentacao_dtVenctoEfetiva").focus(
        function () {
            if ($("#movimentacao_dtVencto").val() != '' && (!dtVenctoS || dtVenctoS != $("#movimentacao_dtVencto").val())) {
                dtVenctoS = $("#movimentacao_dtVencto").val();
                $.getJSON(Routing.generate('findProximoDiaUtilFinanceiro') + '/?dia=' + encodeURIComponent($("#movimentacao_dtVencto").val()))
                    .done(
                        function (data) {
                            $("#movimentacao_dtVenctoEfetiva").val(data.dia);
                        });
            }
        });


    /**
     *
     */
    function loadCarteiras(cheque) {
        $.getJSON(
            Routing.generate('fin_carteira_select2json') + (cheque ? '?cheque=true' : ''),
            function (results) {
                results.unshift({"id": "", "text": "Selecione..."});
                $('#movimentacao_carteira').empty().trigger("change");
                $("#movimentacao_carteira").select2({
                        data: results,
                        width: '100%'
                    }
                );
                // Se veio o valor do PHP...
                if ($('#movimentacao_carteira').data('val')) {
                    $('#movimentacao_carteira').val($('#movimentacao_carteira').data('val')).trigger('change').trigger('select2:select');
                }
            });
    }


    $('#movimentacao_tipoLancto').on('select2:select', function (e) {
        if (!e || !e.params || !e.params.data) return;
        let tipoLancto = e.params.data.id;
        console.log('tipoLancto = ' + tipoLancto);
        if (tipoLancto) {
            if (tipoLancto.includes('CHEQUE')) {
                $('#divCamposCheque').css('display', '');
            } else {
                $('#divCamposCheque').css('display', 'none');
            }

            if (tipoLancto === 'CHEQUE_PROPRIO') {
                // recarrega o campo somente com carteiras que tenham cheque
                loadCarteiras(true);
            } else {
                // recarrega com todas
                loadCarteiras(false);
                $('#movimentacao_chequeBanco').val('').trigger('change');
                $('#movimentacao_chequeAgencia').val('');
                $('#movimentacao_chequeConta').val('');
            }

            if (tipoLancto == 'TRANSF_PROPRIA') {
                window.location.href = Routing
            }
        }
    });

    $('#movimentacao_tipoLancto').trigger('select2:select', {data: {id: 'GERAL'}});


    $('#movimentacao_carteira').on('select2:select', function (e) {
        if (!e || !e.params || !e.params.data) return;
        let carteira = e.params.data;
        if ($('#movimentacao_tipoLancto').val() === 'CHEQUE_PROPRIO') {
            let bancoId = carteira.bancoId;

            $('#movimentacao_chequeBanco').val(carteira.bancoId).trigger('change');
            $('#movimentacao_chequeAgencia').val(carteira.agencia);
            $('#movimentacao_chequeConta').val(carteira.conta);
        }
    });


    function checkExibirCamposRecorrente() {
        let selected = $("#movimentacao_recorrente option:selected").val();
        if (selected === true || selected > 0) {
            $('#camposRecorrente').css('display', '');
        } else {
            $('#camposRecorrente').css('display', 'none');
        }
    }

    $('#movimentacao_recorrente').on('change', function () {
        checkExibirCamposRecorrente();
    });

    checkExibirCamposRecorrente();


});
