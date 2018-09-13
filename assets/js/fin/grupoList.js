'use strict';

let listId = "#grupoList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'descricao',
            title: 'Descrição'
        },
        {
            name: 'e.ativo',
            data: 'ativo',
            title: 'Ativo',
            render: function (data, type, row) {
                return data ? 'SIM' : 'NÃO';
            },
            className: 'text-center'

        },
        {
            name: 'c.descricao',
            data: 'carteiraPagantePadrao',
            title: 'Carteira Padrão',
            render: function (data, type, row) {
                return data.descricao
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

function getDatatablesColumnDefs() {
    return [

    ]
}


$(document).ready(function () {

    $(listId).DataTable({
        paging: true,
        serverSide: true,
        ajax: {
            'url': $(listId).data('listajaxurl'),
            'type': 'POST',
            'data': function (data) {
                data.formPesquisar = $('#formPesquisar').serialize()
            }

        },
        searching: false,
        columns: getDatatablesColumns(),
        columnDefs: getDatatablesColumnDefs(),
        "language": {
            "url": "/build/static/datatables-Portuguese-Brasil.json"
        }
    });

});

