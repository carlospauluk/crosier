'use strict';

import $ from 'jquery';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


import 'summernote/dist/summernote-bs4.js';

Routing.setRoutingData(routes)


$(document).ready(function () {
    let $marca = $('#oc_product_marca_id');
    let $depto = $('#oc_product_depto_id');
    let $descricao = $('#oc_product_descricao');

    $descricao.summernote();


    $.ajax({
            dataType: "json",
            async: false,
            url: Routing.generate('oc_manufacturer_select2json'),
            type: 'GET'
        }
    ).done(function (results) {
        // o valor por ter vindo pelo value ou pelo data-val (ou por nenhum)
        let val = $marca.val();
        $marca.empty().trigger("change");

        results.unshift({"id": "", "text": ""});

        $marca.select2({
                placeholder: "Selecione...",
                data: results,
                width: '100%'
            }
        );
        // Se veio o valor do PHP...
        if (val) {
            $marca.val(val).trigger('change');
        }
    });


    $.ajax({
            dataType: "json",
            async: false,
            url: Routing.generate('oc_category_select2json'),
            type: 'GET'
        }
    ).done(function (results) {
        // o valor por ter vindo pelo value ou pelo data-val (ou por nenhum)
        let val = $depto.val();
        $depto.empty().trigger("change");

        results.unshift({"id": "", "text": ""});

        $depto.select2({
                placeholder: "Selecione...",
                data: results,
                width: '100%'
            }
        );
        // Se veio o valor do PHP...
        if (val) {
            $depto.val(val).trigger('change');
        }
    });




});