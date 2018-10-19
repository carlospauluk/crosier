'use strict';

import $ from "jquery";
import Numeral from "numeral";
import CrosierMasks from "../crosier/CrosierMasks";

// import routes from '../../static/fos_js_routes.json';
// import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
// Routing.setRoutingData(routes);

class ParcelamentoForm {

    static gerarParcelas() {
        let qtdeParcelas = Numeral($("#parcelamento_qtdeParcelas").val()).value();
        let valorParcela = Numeral($("#parcelamento_valorParcela").val()).value();
        let valorTotal = Numeral($("#parcelamento_valorTotal").val()).value();
        let diaFixo = $("#parcelamento_diaFixo").val();

        let tbody = $('#tbody_parcelas');
        tbody.html('');

        let params = {
            "qtdeParcelas": qtdeParcelas,
            "valorTotal": valorTotal,
            "valorParcela": valorParcela,
            "diaFixo": diaFixo,
            "movimentacao": $('form[name="movimentacao"]').serialize()
        };

        $.ajax({
                dataType: "json",
                url: Routing.generate('fin_parcelamento_gerarParcelas'),
                data: params,
                type: 'POST',
                success: function (results) {

                    $.each(results, function (i, parcela) {
                        let tr = $('<tr>');
                        tr.append($('<td>').html(parcela.numParcela)); // numParcela
                        tr.append($('<td>').html(
                            '<input type="text" name="parcela[' + i + '][dtVencto]" required="required" class="crsr-date form-control" value="' + parcela.dtVencto + '" maxlength="10">'
                        )); // dtVencto
                        tr.append($('<td>').html(
                            '<input type="text" name="parcela[' + i + '][dtVenctoEfetiva]" required="required" class="crsr-date form-control" value="' + parcela.dtVenctoEfetiva + '" maxlength="10">'
                        )); // dtVenctoEfetiva
                        tr.append($('<td>').html(
                            '<input type="text" name="parcela[' + i + '][documentoNum]" required="required" class="form-control" value="' + parcela.documentoNum + '">'
                        )); // numDocumento

                        tr.append($('<td class="text-right">').html(
                            `<div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$ </span>
                                </div>
                                <input type="text" name="parcela[` + i + `][valor]" required="required" class="crsr-money form-control" value="` + parcela.valor + `">
                            </div>`
                        )); // Valor
                        tbody.append(tr);
                    });

                    CrosierMasks.maskAll();
                }
            }
        );
    }

    static calcularValorTotal() {
        let qtdeParcelas = Numeral($("#parcelamento_qtdeParcelas").val()).value();
        let valorParcela = Numeral($("#parcelamento_valorParcela").val()).value();
        let valorTotal = Numeral($("#parcelamento_valorTotal").val()).value();

        if (qtdeParcelas) {
            // Se o valorParcela estiver setado, o valorTotal é calculado por ele
            if (valorParcela) {
                valorTotal = valorParcela * qtdeParcelas;
                $("#parcelamento_valorTotal").val(Numeral(valorTotal).format('0,0.00'));
            } else {
                valorParcela = parseFloat((valorTotal / qtdeParcelas).toFixed(2));
                let numeralValorParcela = Numeral(parseFloat(valorParcela));
                $("#parcelamento_valorParcela").val(Numeral(valorParcela).format('0,0.00'));

                // Já refaz a conta para o valorTotal corresponder
                valorTotal = valorParcela * qtdeParcelas;
                $("#parcelamento_valorTotal").val(Numeral(valorTotal).format('0,0.00'));
            }
        }
    }

    static salvarParcelas() {
        let formParcelas = $('form[id=parcelas]');
        $('input[type=hidden][id=movimentacao]').val($('form[name="movimentacao"]').serialize());
        formParcelas.submit();
    }

}

$(document).ready(function () {

    $('#parcelamento_btnGerar').click(function () {
        ParcelamentoForm.gerarParcelas()
    });

    $("#parcelamento_valorParcela,#parcelamento_valorTotal").focus(function () {
        ParcelamentoForm.calcularValorTotal()
    });

    $("#parcelamento_valorTotal,#parcelamento_valorParcela,#parcelamento_qtdeParcelas").blur(function () {
        if ($(this).val() === '' || parseFloat($(this).val()) === 0.0) {
            $("#parcelamento_valorParcela").val('0,00');
            $("#parcelamento_valorTotal").val('0,00');
        }
    });

});

// Defino como global para que possa ser executado no layout.js > $('#confirmationModal') > executeFunctionByName...
global.ParcelamentoForm = ParcelamentoForm;



