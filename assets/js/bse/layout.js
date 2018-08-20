'use strict';

import $ from 'jquery';

global.$ = $; // manter isso até remover todos os <script>'s dos templates

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

import 'select2/select2.css';
import 'select2';

import 'jquery-mask-plugin';
import 'jquery-maskmoney/dist/jquery.maskMoney.js';

import toastr from 'toastr';
import 'toastr/build/toastr.css';

// como adicionar o crosier.js e crosier.css ???

import '../../css/crosier/crosier.css';

import CrosierMasks from '../crosier/CrosierMasks';

$(document).ready(function () {


    CrosierMasks.maskDateTimes();
    CrosierMasks.maskMoneys();
    CrosierMasks.maskDecs();
    CrosierMasks.maskCPF_CNPJ();
    CrosierMasks.maskTelefone9digitos();
    CrosierMasks.maskCEP();

    $('.crsr-datatable').each(function () {
        if ($.fn.dataTable.isDataTable(this)) {
            table = this.DataTable();
        } else {
            $(this).DataTable({
                paging: false,
                searching: false
            });
        }
    });


    $(document).ajaxStart(function () {
        Pace.restart();
    });

    $('#confirmationModal').on('show.bs.modal', function (e) {
        $('#btnConfirmationModalYes', this)
            .data('form', $(e.relatedTarget).data('form'))
            .data('url', $(e.relatedTarget).data('url'))
            .data('token', $(e.relatedTarget).data('token'));
    });
    //Bind click to OK button within popup
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

});

