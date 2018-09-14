'use strict';

import Moment from 'moment';

import Numeral from 'numeral';
import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#carteiraList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.codigo',
            data: 'codigo',
            title: 'Código'
        },
        {
            name: 'e.descricao',
            data: 'descricao',
            title: 'Descrição'
        },
        {
            name: 'e.dtConsolidado',
            data: 'dtConsolidado',
            title: 'Dt Consolidado',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'

        },
        {
            name: 'e.limite',
            data: 'limite',
            title: 'Limite',
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

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

