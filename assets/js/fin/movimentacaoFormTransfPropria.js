'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Moment from 'moment';


Routing.setRoutingData(routes)


$(document).ready(function () {

    $("#movimentacao_transf_propria_carteira").select2({
        placeholder: ''
    });

    $("#movimentacao_transf_propria_carteiraDestino").select2();

    $("#movimentacao_transf_propria_modo").select2();

    $("#movimentacao_transf_propria_pessoa").select2({
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


    $("#movimentacao_transf_propria_dtMoviment").focus(function () {
        if ($("#movimentacao_transf_propria_dtMoviment").val() == '') {
            $("#movimentacao_transf_propria_dtMoviment").val(Moment().format('DD/MM/YYYY'));
        }
    });

    /**
     *
     */
    function loadCarteirasDestino() {
        $.getJSON(
            Routing.generate('fin_carteira_select2json'),
            function (results) {
                results.unshift({"id": "", "text": "Selecione..."});

                for(let i = results.length - 1; i >= 0; i--) {
                    if(results[i].id === $('#movimentacao_transf_propria_carteira').val()) {
                        results.splice(i, 1);
                        break;
                    }
                }

                $('#movimentacao_transf_propria_carteiraDestino').empty().trigger("change");
                $("#movimentacao_transf_propria_carteiraDestino").select2({
                        data: results,
                        width: '100%'
                    }
                );
                // Se veio o valor do PHP...
                if ($('#movimentacao_transf_propria_carteiraDestino').data('val')) {
                    $('#movimentacao_transf_propria_carteiraDestino').val($('#movimentacao_transf_propria_carteiraDestino').data('val')).trigger('change').trigger('select2:select');
                }
            });
    }


    $('#movimentacao_transf_propria_carteira').on('select2:select', function (e) {
        loadCarteirasDestino();
    });


});
