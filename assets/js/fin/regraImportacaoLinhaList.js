'use strict';

import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#regraImportacaoLinhaList";

import Utils from '../crosier/Utils';

function getDatatablesColumns() {
    return [
        {
            name: 'e.regraRegexJava',
            data: 'regraRegexJava',
            title: 'Regex',
            render: function (data, type, row) {
                return Utils.escapeHtml(data);
            }
        },
        {
            name: 'carteira.descricaoMontada',
            data: 'carteira',
            title: 'Carteira',
            render: function (data, type, row) {
                return data ? data.descricaoMontada : '';
            }
        },
        {
            name: 'e.tipoLancto',
            data: 'tipoLancto',
            title: 'Tipo Lancto'
        },
        {
            name: 'e.status',
            data: 'status',
            title: 'Status'
        },
        {
            name: 'e.modo',
            data: 'modo',
            title: 'Modo',
            render: function (data, type, row) {
                return data ? data.descricaoMontada : '';
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

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());