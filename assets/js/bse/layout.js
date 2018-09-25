'use strict';

import $ from 'jquery';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap';

import 'popper.js';

import 'pace-progress/themes/black/pace-theme-barber-shop.css';
import Pace from 'pace-progress';

import 'perfect-scrollbar';

import '@fortawesome/fontawesome-free/css/all.css';

import 'flag-icon-css/css/flag-icon.css';


import 'simple-line-icons/css/simple-line-icons.css';


import '@coreui/coreui';
import '@coreui/coreui/dist/css/coreui.css';


import 'datatables/media/css/jquery.dataTables.css';
import 'datatables';

import 'select2/dist/css/select2.css';
import 'select2';

import 'jquery-mask-plugin';
import 'jquery-maskmoney/dist/jquery.maskMoney.js';

import toastr from 'toastr';
import 'toastr/build/toastr.css';

import Moment from 'moment';
import 'moment/locale/pt-br';
import Numeral from 'numeral';
import 'numeral/locales/pt-br.js'
import '../../css/crosier/crosier.css';

import CrosierMasks from '../crosier/CrosierMasks';


Moment().locale('pt-BR');

Numeral.locale('pt-br');


$(document).ready(function () {


    CrosierMasks.maskDateTimes();
    CrosierMasks.maskMoneys();
    CrosierMasks.maskDecs();
    CrosierMasks.maskCPF_CNPJ();
    CrosierMasks.maskTelefone9digitos();
    CrosierMasks.maskCEP();


    $(document).ajaxStart(function () {
        Pace.restart();
    });

    $('#confirmationModal').on('show.bs.modal', function (e) {
        $('#btnConfirmationModalYes', this)
            .data('form', $(e.relatedTarget).data('form'))
            .data('url', $(e.relatedTarget).data('url'))
            .data('function', $(e.relatedTarget).data('function'))
            .data('token', $(e.relatedTarget).data('token'));
    });


    $('#confirmationModal').on(
        'click',
        '#btnConfirmationModalYes',
        function (e) {
            if ($(this).data('url')) {
                var url = $(this).data('url');
                var token = $(this).data('token');
                var form = $('<form></form>').attr("method", "post").attr(
                    "action", url);
                form.append($('<input></input>').attr("type", "hidden").attr(
                    "name", "token").attr("value", token));
                $(form).appendTo('body').submit();
            } else if ($(this).data('form')) {
                $("[name='" + $(this).data('form') + "']").submit();
            }

        });

    $('.FLASHMESSAGE').each(function () {
        if ($(this).hasClass('FLASHMESSAGE_SUCCESS')) {
            toastr.success($(this).html());
        } else if ($(this).hasClass('FLASHMESSAGE_WARNING')) {
            toastr.warning($(this).html());
        } else if ($(this).hasClass('FLASHMESSAGE_INFO')) {
            toastr.info($(this).html());
        } else if ($(this).hasClass('FLASHMESSAGE_ERROR')) {
            toastr.error($(this).html());
        }
    });

    // Javascript to enable link to tab
    let url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
        window.scrollTo(0, 0);
    });


    /**
     * Montagem dos select2 automáticos.
     */
    $('.autoSelect2').each(function () {
        let elem = $(this);
        $.getJSON(
            Routing.generate($(this).data('route')),
            function (results) {
                elem.select2({
                    data: results,
                    sorter: function (data) {
                        return data.sort(function (a, b) {
                            a = a.text.toLowerCase();
                            b = b.text.toLowerCase();
                            if (a > b) {
                                return 1;
                            } else if (a < b) {
                                return -1;
                            }
                            return 0;
                        });
                    }
                });
                elem.val(elem.val()).trigger('change').trigger('select2:select');
            }
        );
    });

});

global.$ = $; // manter isso até remover todos os <script>'s dos templates