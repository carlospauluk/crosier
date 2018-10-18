'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Numeral from "numeral";


Routing.setRoutingData(routes)


$(document).ready(function () {

    function gerarParcelas() {
        let qtdeParcelas = Numeral($("#parcelamento_qtdeParcelas").val()).value();

        let tbody = $('#tbody_parcelas');
        tbody.html('');
        for (let i = 0; i < qtdeParcelas; i++) {
            let tr = $('<tr>');
            tr.append($('<td>').html(i + 1)); // #
            tr.append($('<td>').html(i + 1)); // #
            tr.append($('<td>').html(i + 1)); // #
            tr.append($('<td>').html(i + 1)); // #
            tr.append($('<td>').html($("#parcelamento_valorParcela").val())); // Valor
            tbody.append(tr);
        }
    }

    function calcularValores() {

        console.log('calcularValoreS()');

        let qtdeParcelas = Numeral($("#parcelamento_qtdeParcelas").val()).value();

        let valorParcela = Numeral($("#parcelamento_valorParcela").val()).value();
        let valorTotal = Numeral($("#parcelamento_valorTotal").val()).value();

        if (qtdeParcelas) {
            if (valorParcela) {
                valorTotal = valorParcela * qtdeParcelas;
                $("#parcelamento_valorTotal").val(Numeral(valorTotal).format('0,0.00'));
            } else {
                valorParcela = parseFloat((valorTotal / qtdeParcelas).toFixed(2));
                let numeralValorParcela = Numeral(parseFloat(valorParcela));
                $("#parcelamento_valorParcela").val(Numeral(valorParcela).format('0,0.00'));

                valorTotal = valorParcela * qtdeParcelas;
                $("#parcelamento_valorTotal").val(Numeral(valorTotal).format('0,0.00'));
            }
        }

        gerarParcelas();
    }


    $("#parcelamento_valorParcela,#parcelamento_valorTotal").focus(function () {
        calcularValores()
    });

    $("#parcelamento_valorTotal,#parcelamento_valorParcela,#parcelamento_qtdeParcelas").blur(function () {
        if ($(this).val() == '' || parseFloat($(this).val()) == 0.0) {
            $("#parcelamento_valorParcela").val('0,00');
            $("#parcelamento_valorTotal").val('0,00');
        }
    });


});
