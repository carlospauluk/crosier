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
    let $divCamposGrupo = $('#divCamposGrupo');
    let $grupo = $('#grupo'); // não é um campo do form
    let $grupoItem = $('#movimentacao_grupoItem');
    let $categoria = $('#movimentacao_categoria');
    let $centroCusto = $('#movimentacao_centroCusto');
    let $modo = $("#movimentacao_modo");
    let $divCamposValores = $("#divCamposValores");
    let $valor = $("#movimentacao_valor");
    let $descontos = $("#movimentacao_descontos");
    let $acrescimos = $("#movimentacao_acrescimos");
    let $valorTotal = $("#movimentacao_valor_total");
    let $dtMoviment = $("#movimentacao_dtMoviment");
    let $dtVencto = $("#movimentacao_dtVencto");
    let $dtVenctoEfetiva = $("#movimentacao_dtVenctoEfetiva");
    let $dtPagto = $("#movimentacao_dtPagto");
    let $divCamposCheque = $('#divCamposCheque');
    let $chequeBanco = $('#movimentacao_chequeBanco');
    let $chequeAgencia = $('#movimentacao_chequeAgencia');
    let $chequeConta = $('#movimentacao_chequeConta');
    let $divCamposDocumento = $('#divCamposDocumento');
    let $documentoBanco = $('#movimentacao_documentoBanco');
    let $documentoNum = $('#movimentacao_documentoNum');
    let $pessoa = $('#movimentacao_pessoa');
    let $divCamposRecorrencia = $('#divCamposRecorrencia');
    let $recorrente = $('#movimentacao_recorrente');
    let $divCamposCartao = $('#divCamposCartao');
    let $bandeiraCartao = $('#movimentacao_bandeiraCartao');
    let $operadoraCartao = $('#movimentacao_operadoraCartao');

    // "cachê" para grupos e itens
    let grupoData = null;

    // "cachê" para não precisar ir toda hora buscar via ajax
    let carteiraData = null;

    // Todos os campos
    let camposTodos = [
        $movimentacao_id,
        $carteira,
        $carteiraDestino,
        $tipoLancto,
        $divCamposGrupo,
        $categoria,
        $centroCusto,
        $modo,
        $divCamposValores,
        $valor,
        $descontos,
        $acrescimos,
        $valorTotal,
        $dtMoviment,
        $dtVencto,
        $dtVenctoEfetiva,
        $dtPagto,
        $divCamposCheque,
        $chequeBanco,
        $chequeAgencia,
        $chequeConta,
        $divCamposDocumento,
        $documentoBanco,
        $documentoNum,
        $pessoa,
        $divCamposRecorrencia,
        $recorrente,
        $bandeiraCartao,
        $operadoraCartao,
    ];

    // Campos que não são mostrados em tal tipoLancto
    let camposEscond = {
        GERAL: [
            $carteiraDestino,
            $divCamposGrupo
        ],
        TRANSF_PROPRIA: [
            $categoria,
            $divCamposGrupo,
            $dtVencto,
            $dtVenctoEfetiva,
            $dtPagto,
            $pessoa,
            $divCamposDocumento
        ],
        GRUPO: [
            $modo,
            $carteira,
            $carteiraDestino,
            $dtVencto,
            $dtVenctoEfetiva,
            $dtPagto
        ]
    };

    /**
     * Método principal. Remonta o formulário de acordo com as regras.
     */
    function handleFormRules() {
        // São esses 3 campos que definem o comportamento do formulário.
        handleTipoLanctoRules();
        handleModoRules();
        handleCarteiraRules();
    }

    /**
     * Regras de acordo com o tipoLancto.
     */
    function handleTipoLanctoRules() {

        let $tipoLanctoSelected = $tipoLancto.find(':selected').val();

        // Filtro por tipoLancto
        let camposVisiveis = $.grep(camposTodos, function (e) {
            return camposEscond[$tipoLanctoSelected].indexOf(e) === -1;
        });

        // Esconde todos
        camposTodos.forEach(function (campo) {
            if (campo.prop('nodeName') === 'DIV') { // para os casos das divCampos...
                campo.css('display', 'none');
            } else {
                campo.closest('.form-group.row').css('display', 'none');
            }
        });

        // Mostra só os correspondentes ao tipoLancto
        camposVisiveis.forEach(function (campo) {
            if (campo.prop('nodeName') === 'DIV') { // para os casos das divCampos...
                campo.css('display', '');
            } else {
                campo.closest('.form-group.row').css('display', '');
            }
        });

        if ($tipoLanctoSelected === 'TRANSF_PROPRIA') {
            buildCarteiraDestino();
        }

    }

    /**
     * Regras de acordo com o campo modo.
     */
    function handleModoRules() {
        let modo = $modo.find(':selected').text();

        if (modo.includes('CHEQUE')) {
            $divCamposCheque.css('display', '');

            // se for 'CHEQUE PRÓPRIO' já preenche com os dados da carteira.
            if (modo.includes('PRÓPRIO')) {
                let carteira = $carteira.find(':selected').data('data');
                $chequeBanco.val(carteira['bancoId']).trigger('change');
                $chequeAgencia.val(carteira['agencia']);

                $chequeConta.val(carteira['conta']);

                // Reconstrói o campo carteira com somente carteiras que possuem cheques
                buildCarteira({'cheque': true});
                return; // para não executar as linhas abaixo.
            }
        } else if (modo.includes('CARTÃO')) {
            $divCamposCartao.css('display', '');
            return; // para não executar as linhas abaixo.
        }

        // Não sendo nem CHEQUE nem CARTÃO, limpa tudo
        $divCamposCheque.css('display', 'none');
        buildCarteira(); // se não for cheque, monta novamente com todas as carteiras
        $chequeBanco.val('').trigger('change');
        $chequeAgencia.val('');
        $chequeConta.val('');

        $divCamposCartao.css('display', 'none');
        $bandeiraCartao.val('').trigger('change');
        $operadoraCartao.val('').trigger('change');
    }

    /**
     * Regras de acordo com o campo carteira.
     */
    function handleCarteiraRules() {

    }


    /**
     * Busca os dados de carteiras via ajax, ou apenas retorna se já buscado.
     * @param params
     * @returns {*}
     */
    function getCarteiraData(params) {
        if (!params) params = {};
        // Para chamar por ajax apenas 1 vez
        if (!carteiraData) {
            $.ajax({
                    dataType: "json",
                    async: false,
                    url: Routing.generate('fin_carteira_select2json'),
                    type: 'POST'
                }
            ).done(function (results) {
                results.unshift({"id": '', "text": ''});
                carteiraData = results;
            });
        }
        return carteiraData;

    }

    /**
     * (Re)constrói o campo carteira de acordo com os parâmetros passados (se foram passados).
     * @param params
     */
    function buildCarteira(params) {
        if (!params) params = {};

        let $carteiraSelected = $carteira.find(':selected');
        let val = $carteiraSelected.val() ? $carteiraSelected.val() : $carteiraSelected.data('val');

        $carteira.empty().trigger("change");

        let _carteira_data =
            // percorre o carteira_data vai colocando no _carteira_data se retornar true
            $.grep(getCarteiraData(params), function (e) {
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
        if (val) {
            $carteira.select2("val", val);
            $carteira.trigger('change');
        }
    }


    /**
     * (Re)constrói o campo carteiraDestino (removendo o valor selecionado no campo carteira).
     */
    function buildCarteiraDestino() {
        if (!$carteiraDestino.is(":visible")) return;

        getCarteiraData(); // chamo para inicializar o carteiraData caso ainda não tenha sido
        let carteiraDataDestino = [];

        for (let i = 0 ; i < carteiraData.length; i++) {
            if (carteiraData[i].id && carteiraData[i].id !== $carteira.val()) {
                carteiraDataDestino.push(carteiraData[i]);
            }
        }
        carteiraDataDestino.unshift({"id": '', "text": ''})

        $carteiraDestino.empty().trigger("change");
        $carteiraDestino.select2({
                placeholder: "Selecione...",
                data: carteiraDataDestino,
                width: '100%'
            }
        );

        if ($carteiraDestino.data('val')) {
            $carteiraDestino.val($carteiraDestino.data('val'));
            $carteiraDestino.trigger('change');
        }
    }


    /**
     * Constrói o campo tipoLancto.
     */
    function buildTiposLancto() {

        if ($tipoLancto.data('val')) {
            $tipoLancto.select2();
        } else {
            $.ajax({
                    dataType: "json",
                    async: false,
                    url: Routing.generate('fin_movimentacao_getTiposLanctos'),
                    data: {"formMovimentacao": $('form[name="movimentacao"]').serialize()},
                    type: 'POST'
                }
            ).done(function (results) {

                results = $.map(results['tiposLanctos'], function (o) {
                    return {id: o.val, text: o.title, route: o.route};
                });

                $tipoLancto.empty().trigger("change");
                $tipoLancto.select2({
                        data: results,
                        width: '100%'
                    }
                );
                // Se veio o valor do PHP...
                if ($tipoLancto.data('val')) {
                    $tipoLancto.val($tipoLancto.data('val')).trigger('change');
                }
            });
        }
    }

    function buildCategoria() {
        $.ajax({
                dataType: "json",
                async: false,
                url: Routing.generate('fin_categoria_select2json') + '?tipoLancto=' + $tipoLancto.val(),
                type: 'GET'
            }
        ).done(function (results) {
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
                $categoria.val($categoria.data('val')).trigger('change');
            }
        });
    }

    /**
     * Constrói os campos de grupo e grupoItem.
     */
    function buildGrupos() {
        /**
         * Handler para evento no select2 #grupo
         */
        $grupo.on('select2:select', function () {
            $grupoItem.empty().trigger("change");
            let results = $(this).select2('data')[0]['itens'];
            results.unshift({"id": "", "text": "Selecione..."});
            $grupoItem.select2({
                data: results,
                width: '100%'
            });
            if ($grupoItem.data('val')) {
                $grupoItem.val($grupoItem.data('val')).trigger('change').trigger('select2:select');
            }
        });

        $.ajax({
            url: Routing.generate('fin_grupo_select2json') + '?abertos=true',
            dataType: 'json',
            async: false
        }).done(function (result) {
            result.unshift({"id": "", "text": "Selecione..."});
            $grupo.empty().trigger("change");
            $grupo.select2({
                data: result,
                width: '100%'
            });
            if ($grupoItem.data('valpai')) {
                $grupo.val($grupoItem.data('valpai')).trigger('change').trigger('select2:select');
            }
        });

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


    $valor.on('blur', function () {
        resValorTotal()
    });
    $descontos.on('blur', function () {
        resValorTotal()
    });
    $acrescimos.on('blur', function () {
        resValorTotal()
    });

    $dtMoviment.on('focus', function () {
        if ($dtMoviment.val() === '') {
            $dtMoviment.val(Moment().format('DD/MM/YYYY'));
        }
    });

    let dtVenctoS = null;
    $dtVenctoEfetiva.on('focus', function () {
        if ($dtVencto.val() !== '' && (!dtVenctoS || dtVenctoS !== $dtVencto.val())) {
            dtVenctoS = $dtVencto.val();
            $.getJSON(Routing.generate('findProximoDiaUtilFinanceiro') + '/?dia=' + encodeURIComponent($dtVencto.val()))
                .done(
                    function (data) {
                        $("#movimentacao_dtVenctoEfetiva").val(data['dia']);
                    });
        }
    });


    $recorrente.on('change', function () {
        checkExibirCamposRecorrente();
    });

    $carteira.on('select2:select', function () {
        handleFormRules();
    });

    $tipoLancto.on('select2:select', function () {
        handleFormRules()
    });

    $modo.on('select2:select', function () {
        handleFormRules()
    });


    // -----------------

    function initializeForm() {

        $modo.select2({placeholder: "Selecione..."});
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
                processResults: function (data) {
                    let dataNew = $.map(data.results, function (obj) {
                        obj.text = obj['nome'];
                        return obj;
                    });
                    return {results: dataNew};
                },
                cache: true
            },
            minimumInputLength: 1
        });

        buildTiposLancto();
        buildCategoria();
        buildGrupos();
        handleFormRules();
    }


    initializeForm();


})
;
