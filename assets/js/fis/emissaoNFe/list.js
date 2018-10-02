'use strict';

import Moment from 'moment';

import Numeral from 'numeral';
import DatatablesJs from "../../crosier/DatatablesJs";

let listId = "#emissaoNFeList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Id'
        },
        {
            name: 'e.numero',
            data: 'e',
            title: 'Número/Série',
            render: function (data, type, row) {
                return "<b>" + String(data.numero ? data.numero : '0').padStart(6, "0") + "</b>/" + String(data.serie).padStart(3, "0");
            }
        },
        {
            name: 'e.pessoaDestinatario',
            data: 'e.pessoaDestinatario',
            title: 'Destinatário/Remetente',
            render: function (data, type, row) {
                return '<span class="' + (data.tipoPessoa == 'PESSOA_JURIDICA' ? 'cnpj' : 'cpf') + '">' + data.documento + '</span><br />' + data.nome;
            }
        },
        {
            name: 'e.dtEmissao',
            data: 'e.dtEmissao',
            title: 'Dt Emissão',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'

        },
        {
            name: 'e.infoStatus',
            data: 'e.infoStatus',
            title: 'Status'
        },
        {
            name: 'e.updated',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let routeedit = $(listId).data('routeedit');
                let url = routeedit + '/' + data.id;
                return "<button " +
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