'use strict';

import $ from 'jquery';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes)


function prepareForm() {

    let $fornecedor = $('#produto_fornecedor');

    let url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
        window.scrollTo(0, 0);
    });


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


}

$(document).ready(function () {
    prepareForm();
});