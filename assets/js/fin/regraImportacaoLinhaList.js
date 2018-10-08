'use strict';

import DatatablesJs from "../crosier/DatatablesJs";

import Moment from 'moment';

let listId = "#regraImportacaoLinhaList";

import Utils from '../crosier/Utils';

function getDatatablesColumns() {
    return [
        {
            name: 'e.regraRegexJava',
            data: 'e.regraRegexJava',
            title: 'Regex',
            render: function (data, type, row) {
                return Utils.escapeHtml(data);
            }
        },
        {
            name: 'carteira.descricaoMontada',
            data: 'e.carteira',
            title: 'Carteira',
            render: function (data, type, row) {
                return data ? data.descricaoMontada : '';
            }
        },
        {
            name: 'e.tipoLancto',
            data: 'e.tipoLancto',
            title: 'Tipo Lancto'
        },
        {
            name: 'e.status',
            data: 'e.status',
            title: 'Status'
        },
        {
            name: 'e.modo',
            data: 'e.modo',
            title: 'Modo',
            render: function (data, type, row) {
                return data ? data.descricaoMontada : '';
            }
        },
        {
            name: 'e.updated',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data.id;
                return "<button " +
                    "data-placement=\"bottom\" data-toggle=\"tooltip\" data-html=\"true\" title=\"" + data.userUpdated.nome + "\"" +
                    "type=\"button\" class=\"btn btn-primary\" onclick=\"window.location.href='" + url + "'\">" +
                    "<i class=\"fas fa-wrench\" aria-hidden=\"true\"></i></button><br />" +
                    "<span class=\"badge badge-secondary\" style=\"cursor: pointer\"" +
                    "data-placement=\"bottom\" data-toggle=\"tooltip\" data-html=\"true\" title=\"" + data.userUpdated.nome + "\">" + Moment.unix(data.updated.timestamp).format('DD/MM/YYYY HH:mm:ss') + "</span>";
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());