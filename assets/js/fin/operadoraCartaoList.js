'use strict';


import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#operadoraCartaoList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'descricao',
            title: 'Descrição'
        },
        {
            name: 'c.descricao',
            data: 'carteira',
            title: 'Carteira',
            render: function (data, type, row) {
                return data.descricao;
            }
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