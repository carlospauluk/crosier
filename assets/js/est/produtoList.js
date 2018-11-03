'use strict';

import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#produtoList";

import Moment from 'moment';

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Id'
        },
        {
            name: 'e.reduzido',
            data: 'e.reduzido',
            title: 'Reduzido'
        },
        {
            name: 'e.descricao',
            data: 'e.descricao',
            title: 'Descrição'
        },
        {
            name: 'e.fornecedor.pessoa.nomeFantasia',
            data: 'e.fornecedor',
            title: 'Fornecedor',
            render: function (data, type, row) {
                return data.codigo + ' - ' +  data.pessoa.nomeFantasia;
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