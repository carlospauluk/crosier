'use strict';

import DatatablesJs from "../crosier/DatatablesJs";
import Moment from "moment";
import Numeral from "numeral";
import $ from "jquery";

let listId = "#grupoItemList";


function getDatatablesColumns() {
    return [
        {
            name: 'e.descricao',
            data: 'e.descricao',
            title: 'Descrição'
        },
        {
            name: 'e.dtVencto',
            data: 'e.dtVencto',
            title: 'Dt Vencto',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'

        },
        {
            name: 'e.valorInformado',
            data: 'e.valorInformado',
            title: 'Valor Inf',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('$ 0.0,[00]');
            }
        },
        {
            name: 'e.fechado',
            data: 'e.fechado',
            title: 'Fechado',
            render: function (data, type, row) {
                return data ? 'SIM' : 'NÃO';
            }
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



