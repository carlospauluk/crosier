'use strict';


import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#bancoList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.codigoBanco',
            data: 'codigoBanco',
            title: 'Código'
        },
        {
            name: 'e.nome',
            data: 'nome',
            title: 'Nome'
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

