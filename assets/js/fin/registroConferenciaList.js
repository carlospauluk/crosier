'use strict';

import Moment from "moment";
import Numeral from "numeral";

let listId = "#registroConferenciaList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'descricao',
            title: 'Descrição'
        },
        {
            name: 'e.dtRegistro',
            data: 'dtRegistro',
            title: 'Dt Registro',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'

        },
        {
            name: 'c.descricao',
            data: 'carteira',
            title: 'Carteira',
            render: function (data, type, row) {
                return data ? data.descricaoMontada : null;
            }
        },
        {
            name: 'e.valor',
            data: 'valor',
            title: 'Valor',
            render: function (data, type, row) {
                return Numeral(parseFloat(data)).format('$ 0.0,[00]')
            },
            className: 'text-right'

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

