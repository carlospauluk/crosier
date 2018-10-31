'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Moment from 'moment';


Routing.setRoutingData(routes);


$(document).ready(function () {

    let $movimentacao_id = $('#movimentacao_id');
    let $carteira = $('#movimentacao_carteira');
    let $carteiraDestino = $('#movimentacao_carteiraDestino');
    let $tipoLancto = $('#movimentacao_tipoLancto');
    let $categoria = $('#movimentacao_categoria');
    let $centroCusto = $('#movimentacao_centroCusto');
    let $modo = $("#movimentacao_modo");
    let $valor = $("#movimentacao_valor");
    let $descontos = $("#movimentacao_descontos");
    let $acrescimos = $("#movimentacao_acrescimos");
    let $valorTotal = $("#movimentacao_valor_total");
    let $dtMoviment = $("#movimentacao_dtMoviment");
    let $dtVencto = $("#movimentacao_dtVencto");
    let $dtVenctoEfetiva = $("#movimentacao_dtVenctoEfetiva");
    let $chequeBanco = $('#movimentacao_chequeBanco');
    let $chequeAgencia = $('#movimentacao_chequeAgencia');
    let $chequeConta = $('#movimentacao_chequeConta');
    let $documentoBanco = $('#movimentacao_documentoBanco');
    let $pessoa = $('#movimentacao_pessoa');
    let $recorrente = $('#movimentacao_recorrente');


    let carteira_data = null;

    function loadCarteiras(params) {
        if (!params) {
            params = {};
        }

        let $carteiraSelected = $('#movimentacao_carteira :selected');

        // Para chamar por ajax apenas 1 vez
        if (!carteira_data) {
            $.ajax({
                    dataType: "json",
                    url: Routing.generate('fin_carteira_select2json'),
                    type: 'POST',
                    success: function (results) {
                        results.unshift({"id": '', "text": ''});
                        carteira_data = results;
                        buildCarteiraSelect2(params);
                    }
                }
            );
        } else {
            buildCarteiraSelect2(params);
        }
    }

    function buildCarteiraSelect2(params) {
        $carteira.empty().trigger("change");


        let _carteira_data =
            // percorre o carteira_data vai colocando no _carteira_data se retornar true
            $.grep(carteira_data, function (e, i) {
                // se não foi passado params
                if ($.isEmptyObject(params)) {
                    return true;
                } else {
                    // se todos os params passados corresponderem
                    let match = true;
                    $.each(params, function (key, value) {
                        match = match && e[key] === value;
                    });
                    return match;
                }
            });


        $carteira.select2({
                placeholder: "Selecione...",
                data: _carteira_data,
                width: '100%'
            }
        );
        // Se já estava setado ou se veio o valor do PHP...
        let $carteiraSelected = $('#movimentacao_carteira :selected');
        let val = $carteiraSelected.val() ? $carteiraSelected.val() : $carteiraSelected.data('val');
        if (val) {
            $carteira.select2("val", val);
            //$carteira.val(val).trigger('change').trigger('select2:select');
            console.log('setei carteira');
        }
    }


    function handleCarteiras() {
        let modo = $('#movimentacao_modo :selected').text();

        if (modo.includes('CHEQUE')) {
            $('#movimentacao_carteira option');
        }
    }


    function loadCarteirasDestino() {
        if (!$carteiraDestino.is(":visible")) return;
        $.getJSON(
            Routing.generate('fin_carteira_select2json'),
            function (results) {
                for (let i = results.length - 1; i >= 0; i--) {
                    if (results[i].id === $carteira.val()) {
                        results.splice(i, 1);
                        break;
                    }
                }

                results.unshift({"id": "", "text": ""});

                $carteiraDestino.empty().trigger("change");
                $carteiraDestino.select2({
                        placeholder: "Selecione...",
                        data: results,
                        width: '100%'
                    }
                );
                // Se veio o valor do PHP...
                if ($carteiraDestino.data('val')) {
                    $carteiraDestino.select2('val', $carteiraDestino.data('val'));
                }
            });
    }


    function handleCamposCheque() {
        if ($tipoLancto.val() === 'CHEQUE_PROPRIO') {
            let carteira = $('#movimentacao_carteira :selected').data('data');
            $chequeBanco.select2('val', carteira.bancoId);
            $chequeAgencia.val(carteira.agencia);
            $chequeConta.val(carteira.conta);
        }

        let modo = $('#movimentacao_modo :selected').text();

        if (modo.includes('CHEQUE')) {
            $('#divCamposCheque').css('display', '');
        } else {
            $('#divCamposCheque').css('display', 'none');
        }

        if (modo.includes('CHEQUE PRÓPRIO')) {
            // recarrega o campo somente com carteiras que tenham cheque
            loadCarteiras({'cheque': true});
        } else {
            // recarrega com todas
            loadCarteiras();
            $chequeBanco.select2('val', '');
            $chequeAgencia.val('');
            $chequeConta.val('');
        }
    }


    function loadTiposLancto() {
        $.ajax({
                dataType: "json",
                url: Routing.generate('fin_movimentacao_getTiposLanctos'),
                data: {"formMovimentacao": $('form[name="movimentacao"]').serialize()},
                type: 'POST',
                success: function (results) {

                    results = $.map(results.tiposLanctos, function (o, i) {
                        return {id: o.val, text: o.title, route: o.route};
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
                }
            }
        );
    }


    function tipoLanctoChange() {
        let $tipoLanctoSelected = $('#movimentacao_tipoLancto :selected');

        let tipoLancto = $tipoLanctoSelected.text();
        let tipoLanctoVal = $tipoLanctoSelected.val();

        // Some todos e mostra somente os tipoLancto-TODOS e os correspondentes ao tipoLancto selecionado
        let $tipoLancto = $("[class*='tipoLancto-']");
        $tipoLancto.closest('div .form-group.row').css('display', 'none');
        $tipoLancto.closest('.divCampos').css('display', 'none');
        let $tipoLancto_TODOS = $(".tipoLancto-TODOS");
        $tipoLancto_TODOS.closest('div .form-group.row').css('display', '');
        $tipoLancto_TODOS.closest('.divCampos').css('display', '');
        let $tipoLancto_tipoLanctoVal = $(".tipoLancto-" + tipoLanctoVal);
        $tipoLancto_tipoLanctoVal.closest('div .form-group.row').css('display', '');
        $tipoLancto_tipoLanctoVal.closest('.divCampos').css('display', '');

        // Recarrega as categorias, pois tem regra para quais exibir dependendo do tipoLancto
        loadCategorias();
        //
        handleCamposCheque();
    }


    function loadCategorias() {
        $.ajax({
                dataType: "json",
                url: Routing.generate('fin_categoria_select2json') + '?tipoLancto=' + $tipoLancto.val(),
                type: 'GET',
                success: function (results) {
                    $categoria.empty().trigger("change");

                    results.unshift({"id": "", "text": ""});

                    $categoria.select2({
                            placeholder: "Selecione...",
                            data: results,
                            width: '100%'
                        }
                    );
                    // Se veio o valor do PHP...
                    if ($categoria.data('val')) {
                        $categoria.val($categoria.data('val')).trigger('change').trigger('select2:select');
                    }
                }
            }
        );
    }


    function modoChange() {
        let modo = $('#movimentacao_modo :selected').text();
        if (modo) {
            if (modo.includes('CARTÃO')) {
                $('#divCamposCartao').css('display', '');
            } else {
                $('#divCamposCartao').css('display', 'none');
            }

        }
        handleCamposCheque();
        handleCarteiras();
    }


    function resValorTotal() {
        let valor = $valor.maskMoney('unmasked')[0];
        let descontos = $descontos.maskMoney('unmasked')[0];
        let acrescimos = $acrescimos.maskMoney('unmasked')[0];
        let valorTotal = (valor - descontos + acrescimos).toFixed(2);
        $valorTotal.val(valorTotal).maskMoney('mask');
    }


    function checkExibirCamposRecorrente() {
        let selected = $("#movimentacao_recorrente option:selected").val();
        if (selected === true || selected > 0) {
            $('#camposRecorrente').css('display', '');
        } else {
            $('#camposRecorrente').css('display', 'none');
        }
    }


    $valor.on('blur', function (e) {
        resValorTotal()
    });
    $descontos.on('blur', function (e) {
        resValorTotal()
    });
    $acrescimos.on('blur', function (e) {
        resValorTotal()
    });

    $dtMoviment.on('focus', function (e) {
        if ($dtMoviment.val() === '') {
            $dtMoviment.val(Moment().format('DD/MM/YYYY'));
        }
    });

    let dtVenctoS = null;
    $dtVenctoEfetiva.on('focus', function (e) {
        if ($dtVencto.val() !== '' && (!dtVenctoS || dtVenctoS !== $dtVencto.val())) {
            dtVenctoS = $dtVencto.val();
            $.getJSON(Routing.generate('findProximoDiaUtilFinanceiro') + '/?dia=' + encodeURIComponent($dtVencto.val()))
                .done(
                    function (data) {
                        $("#movimentacao_dtVenctoEfetiva").val(data.dia);
                    });
        }
    });


    $recorrente.on('change', function () {
        checkExibirCamposRecorrente();
    });

    $carteira.on('select2:select', function (e) {
        loadCarteirasDestino();
        handleCamposCheque();
    });

    $tipoLancto.on('select2:select', function (e) {
        tipoLanctoChange();
    });

    $modo.on('select2:select', function (e) {
        modoChange();
    });


    $modo.select2({placeholder: "Selecione..."});

    $categoria.select2();

    $centroCusto.select2();

    $documentoBanco.select2();

    $chequeBanco.select2();

    $pessoa.select2({
        ajax: {
            delay: 250,
            url: function (params) {
                console.log(params);
                return Routing.generate('bse_pessoa_findByNome') + '/' + params.term;
            },
            dataType: 'json',
            processResults: function (data, params) {
                let dataNew = $.map(data.results, function (obj) {
                    obj.text = obj.nome;
                    return obj;
                });
                return {results: dataNew};
            },
            cache: true
        },
        minimumInputLength: 1
    });


    checkExibirCamposRecorrente();
    loadTiposLancto();
    loadCarteiras();
    loadCarteirasDestino();
// loadCategorias(); -- não precisa, é chamado no loadTiposLancto()
    tipoLanctoChange();
    modoChange();


})
;
