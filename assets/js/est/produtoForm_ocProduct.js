'use strict';

import $ from 'jquery';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes)


$(document).ready(function () {
    let $fornecedor = $('#produto_fornecedor');

    function buildFornecedor() {

        // o valor por ter vindo pelo value ou pelo data-val (ou por nenhum)
        let val = $fornecedor.val() ? $fornecedor.val() : $fornecedor.data('val');

        $fornecedor.select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    console.log(params);
                    return Routing.generate('est_fornecedor_findByCodigoOuNome') + '/' + params.term;
                },
                dataType: 'json',
                cache: true
            },
            minimumInputLength: 1
        });

        // Se veio o valor do PHP...
        if (val) {
            $fornecedor.val(val).trigger('change');
        }
    }

    function prepareForm() {


        let url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
            window.scrollTo(0, 0);
        });


        buildFornecedor();


    }

    prepareForm();
});