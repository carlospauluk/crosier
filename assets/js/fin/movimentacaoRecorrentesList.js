'use strict';

import Moment from 'moment';

import Numeral from 'numeral';
import DatatablesJs from "../crosier/DatatablesJs";
import $ from "jquery";
import CrosierMasks from "../crosier/CrosierMasks";

let listId = "#movimentacaoRecorrentesList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e.id',
            title: '',
            render: function (data, type, row) {
                return '<input type="checkbox" style="width: 20px" class="form-control checkboxMovimentacao" name="movs[' + data + ']"/>';
            },
            orderable: false,
            sortable: false
        },
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Id'
        },
        {
            name: 'e.descricao',
            data: 'e',
            title: 'Descrição<br/>Sacado/Cedente',
            render: function (data, type, row) {
                return "<b>" + data.descricao + "</b><br />" + (data.pessoa ? data.pessoa.nome : '');
            }
        },
        {
            name: 'e.carteira',
            data: 'e',
            title: 'Carteira<br />Categoria',
            render: function (data, type, row) {
                return '<b>' + data.carteira.descricaoMontada + "</b><br />" + data.categoria.descricaoMontada;
            },
        },
        {
            name: 'e.dtUtil',
            data: 'e.dtUtil',
            title: 'Data',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'

        },
        {
            name: 'e.valorTotal',
            data: 'e',
            title: 'Valor',
            render: function (data, type, row) {
                let val = parseFloat(data.valorTotal);
                return '<span style="background-color:' + (data.codigoSuper == 1 ? 'blue' : 'red') + '">' +  Numeral(val).format('$ 0.0,[00]') + "</span>";
            },
            className: 'text-right'
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



DatatablesJs.makeDatatableJs(listId, getDatatablesColumns(), {paging: false});

$(document).ready(function () {

    let datatables = $(listId).DataTable().on('draw', function () {
        $('#movimentacaoRecorrentesList > thead > tr > th:first').html(
            `<input type="checkbox" class="form-control" style="width: 20px" onclick="$('.checkboxMovimentacao').prop('checked', this.checked);" />`
        );
    });



});


