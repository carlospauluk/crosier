'use strict';


import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#bandeiraCartaoList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'e.descricao',
            title: 'Descrição'
        },
        {
            name: 'm.descricao',
            data: 'e.modo',
            title: 'Modo',
            render: function (data, type, row) {
                return data.descricao
            }
        },
        {
            name: 'e.labels',
            data: 'e.labels',
            title: 'Labels'
        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data.id;
                return "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location.href='" + url + "'\">" +
                    "<i class=\"fas fa-wrench\" aria-hidden=\"true\"></i></button>";
            },
            className: 'text-right'
        }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

