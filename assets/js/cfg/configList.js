'use strict';

let listId = "#configList";

import DatatablesJs from '../crosier/DatatablesJs';

function getDatatablesColumns() {
    return [
        {
            name: 'e.chave',
            data: 'chave',
            title: 'Chave'
        },
        {
            name: 'e.valor',
            data: 'valor',
            title: 'Valor'
        },
        {
            name: 'e.global',
            data: 'global',
            title: 'Global',
            render: function (data, type, row) {
                return data ? 'S' : 'N'
            },
        },
        {
            name: 'e.id',
            data: 'id',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data;
                return "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location.href='" + url + "'\">" +
                    "<i class=\"fas fa-wrench\" aria-hidden=\"true\"></i></button>";
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());