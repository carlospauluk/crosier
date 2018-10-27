'use strict';

import Moment from 'moment';

import $ from "jquery";

$(document).ready(function () {

    $.getJSON(
        Routing.generate('fin_carteira_select2json') + '?caixa=true',
        function (results) {
            results.unshift({"id": "", "text": "Selecione..."});

            $('#filter_carteira').empty().trigger("change");
            $("#filter_carteira").select2({
                    data: results,
                    width: '100%'
                }
            );
            // Se veio o valor do PHP...
            if ($('#filter_carteira').data('val')) {
                $('#filter_carteira').val($('#filter_carteira').data('val')).trigger('change').trigger('select2:select');
            }

            // Setei o 'on' depois do código acima senão caía num loop infinito
            $('#filter_carteira').on('select2:select', function() {
                $('#formPesquisar').submit();
            });
        });



});