'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes)


$(document).ready(function () {
    $("#movimentacao_documentoBanco").select2();

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


    $("#movimentacao_dtVenctoEfetiva").focus(
        function () {
            $.get(
                "{{ url('findProximoDiaUtilFinanceiro') }}/?dia=" + encodeURIComponent($("#movimentacao_dtVencto").val()),
                function (data) {
                    $("#movimentacao_dtVenctoEfetiva").val(data);
                });
        });

});
