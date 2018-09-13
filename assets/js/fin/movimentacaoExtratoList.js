'use strict';

import Moment from 'moment';

import $ from "jquery";

$(document).ready(function () {

    $('#filter_carteira').on('change', function() {
        $('#formPesquisar').submit();
    });

    $('#btn_hoje').on('click', function() {
       $('#filter_dtUtil_i').val(Moment().format('YYYY-MM-DD'));
       $('#filter_dtUtil_f').val(Moment().format('YYYY-MM-DD'));
        $('#formPesquisar').submit();
    });

    $('#btn_ante').on('click', function() {
        $('#filter_dtUtil_i').val($(this).data('ante-periodoi'));
        $('#filter_dtUtil_f').val($(this).data('ante-periodof'));
        $('#formPesquisar').submit();
    });

    $('#btn_prox').on('click', function() {
        $('#filter_dtUtil_i').val($(this).data('prox-periodoi'));
        $('#filter_dtUtil_f').val($(this).data('prox-periodof'));
        $('#formPesquisar').submit();
    });

});