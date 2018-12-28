'use strict';

import Moment from "moment";
import Numeral from "numeral";
import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#registroConferenciaList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'e.descricao',
            title: 'Descrição'
        },
        {
            name: 'e.dtRegistro',
            data: 'e.dtRegistro',
            title: 'Dt Registro',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'

        },
        {
            name: 'c.descricao',
            data: 'e.carteira',
            title: 'Carteira',
            render: function (data, type, row) {
                return data ? data.descricaoMontada : null;
            }
        },
        {
            name: 'e.valor',
            data: 'e.valor',
            title: 'Valor',
            render: function (data, type, row) {
                return Numeral(parseFloat(data)).format('$ 0.0,[00]')
            },
            className: 'text-right'

        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data.id;

                return "<a role=\"button\" class=\"btn btn-primary\" href=\"" + url + "\">" +
                    "<i class=\"fas fa-wrench\" aria-hidden=\"true\"></i></a>";
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());