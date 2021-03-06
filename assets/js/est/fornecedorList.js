'use strict';

import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#fornecedorList";

import Moment from 'moment';

function getDatatablesColumns() {
    return [
        {
            name: 'e.codigo',
            data: 'e.codigo',
            title: 'Código'
        },
        {
            name: 'p.nome',
            data: 'e.pessoa',
            title: 'Nome',
            render: function (data, type, row) {
                return data.nome;
            }
        },
        {
            name: 'e.updated',
            data: 'e.updated',
            title: 'Atualizado',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY HH:mm:ss');
            },
            className: 'text-center'

        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data.id
                return "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location.href='" + url + "'\">" +
                    "<i class=\"fas fa-wrench\" aria-hidden=\"true\"></i></button>";
            }
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());