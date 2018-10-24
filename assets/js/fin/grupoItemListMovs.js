'use strict';

import $ from "jquery";

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


$(document).ready(function () {


    $.getJSON(
        Routing.generate('fin_grupoItem_select2json') + '?pai=' + $("#filter_grupoItem").data('grupo'),
        function (results) {
            $('#filter_grupoItem').empty().trigger("change");
            $("#filter_grupoItem").select2({
                    data: results,
                    width: '100%'
                }
            );
            // Se veio o valor do PHP...
            if ($('#filter_grupoItem').data('grupoitem')) {
                $('#filter_grupoItem').val($('#filter_grupoItem').data('grupoitem')).trigger('change').trigger('select2:select');
            }
        });


    $('#filter_grupoItem').val($('#filter_grupoItem').data('val')).trigger('change').trigger('select2:select');


    $('#filter_grupoItem').on('select2:select', function (e) {
        if (!e || !e.params || !e.params.data) return;
        let grupoItem = e.params.data.id;

        window.location.href = Routing.generate('fin_grupoItem_listMovs') + '/' + grupoItem;
    });


});


Routing.setRoutingData(routes);
