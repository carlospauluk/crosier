'use strict';

import $ from 'jquery';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


Routing.setRoutingData(routes)


$(document).ready(function () {

    let $form = $('#form');
    let $fornecedor = $('#fornecedor');
    let $tamanho = $('#tamanho');
    let $msg = $('#msg');
    let $btnCtrlC = $('#btnCtrlC');

    $fornecedor.select2({
        ajax: {
            delay: 250,
            url: function (params) {
                return Routing.generate('est_fornecedor_findByCodigoOuNome') + '/' + params.term;
            },
            dataType: 'json',
            processResults: function (data) {
                let dataNew = $.map(data, function (obj) {
                    obj.text = obj['pessoa']['nomeFantasia'];
                    console.dir(obj);
                    return obj;
                });
                return {results: dataNew};
            },
            cache: true
        },
        minimumInputLength: 1
    });


    if ($fornecedor.data('val')) {
        $.ajax({
                dataType: "json",
                async: false,
                url: Routing.generate('est_fornecedor_findById') + '/' + $fornecedor.data('val'),
                type: 'GET'
            }
        ).done(function (results) {
            let newOption = new Option(results.pessoa.nomeFantasia, results.id, false, false);
            $fornecedor.append(newOption).trigger('change');

            // let val = $fornecedor.val() ? $fornecedor.val() : $fornecedor.data('val');
            // $fornecedor.val(val).trigger('change');
        });


    }



    $tamanho.select2();


    $btnCtrlC.on('click', function (e) {

        if ($msg.val()) {
            $msg.select();
            document.execCommand("copy");
        }

    });





});