'use strict';

import DatatablesJs from "../crosier/DatatablesJs";

import routes from '../../static/fos_js_routes.json';

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js';

let listId = "#grupoList";

// Gambi pra passar o json certo
Routing.setRoutingData(routes);

function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'e.descricao',
            title: 'Descrição'
        },
        {
            name: 'e.ativo',
            data: 'e.ativo',
            title: 'Ativo',
            render: function (data, type, row) {
                return data ? 'SIM' : 'NÃO';
            },
            className: 'text-center'

        },
        {
            name: 'c.descricao',
            data: 'e.carteiraPagantePadrao',
            title: 'Carteira Padrão',
            render: function (data, type, row) {
                return data.descricao
            }

        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data.id;
                let r = "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location.href='" + url + "'\">" +
                    "<i class=\"fas fa-wrench\" aria-hidden=\"true\"></i></button>";

                r += "<button data-placement=\"bottom\" data-toggle=\"tooltip\" title=\"Ver itens do grupo\"" +
                    "type=\"button\" class=\"btn btn-secondary\" onclick=\"window.location.href='" + Routing.generate('fin_grupoItem_list', {'pai': data.id}) + "'\">" +
                    "<i class=\"fas fa-stream\" aria-hidden=\"true\"></i></button>";

                return r;
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

