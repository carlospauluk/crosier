'use strict';

let listId = "#clienteList";

import Moment from 'moment';

function getDatatablesColumns() {
    return [
        {
            name: 'e.codigo',
            data: 'codigo',
            title: 'Código'
        },
        {
            name: 'p.nome',
            data: 'pessoa',
            title: 'Nome',
            render: function (data, type, row) {
                return data.nome;
            }
        },
        {
            name: 'e.updated',
            data: 'updated',
            title: 'Atualizado',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY HH:mm:ss');
            },
            className: 'text-center'

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
            }
        }
    ];
}


$(document).ready(function () {

    $(listId).DataTable({
        paging: true,
        serverSide: true,
        stateSave: true,
        ajax: {
            'url': $(listId).data('listajaxurl'),
            'type': 'POST',
            'data': function (data) {
                data.formPesquisar = $('#formPesquisar').serialize()
            }

        },
        searching: false,
        columns: getDatatablesColumns(),
        "language": {
            "url": "/build/static/datatables-Portuguese-Brasil.json"
        }
    });

});

