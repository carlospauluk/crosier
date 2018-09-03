'use strict';

import $ from 'jquery';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes)


function prepareForm() {
    let tipoPessoa = $(".TIPO_PESSOA").find(':checked').val();

    if (tipoPessoa == 'PESSOA_FISICA') {
        $(".PESSOA_FISICA").parent().parent().css('display', '');
        $(".PESSOA_JURIDICA").parent().parent().css('display', 'none');
    } else {
        $(".PESSOA_FISICA").parent().parent().css('display', 'none');
        $(".PESSOA_JURIDICA").parent().parent().css('display', '');
    }

    // Javascript to enable link to tab
    let url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
        window.scrollTo(0, 0);
    });


}

$(document).ready(function () {

    $(".TIPO_PESSOA").change(
        function () {
            let dadosPessoa = $('.DADOSPESSOA');
            dadosPessoa.val('');
            dadosPessoa.prop('disabled', false);
            prepareForm();
        });

    prepareForm();

    let documentoAtual;

    function findPessoaByDocumento(documento) {

        if (!documento) return;
        if (documento === documentoAtual) return;
        documentoAtual = documento;

        $.ajax({
            url: Routing.generate('bse_pessoa_findByNome') + '/' + documento.replace(/[^0-9]/g, ""),
            type: 'get',
            dataType: 'json',
            success: function (data) {
                $('input[id=cliente_nome]').val(data.nome);
                $('input[id=cliente_pessoa_id]').val(data.id);
                $('input[id=cliente_razao_social]').val(data.nome);
                $('input[id=cliente_nome_fantasia]').val(data.nomeFantasia);

                $('input[id=cliente_fone1]').val(data.fone1);
                $('input[id=cliente_email]').val(data.email);

                if (data.id) {
                    $('.DADOSPESSOA').prop('disabled', true);
                }
            }
        });
    }

    $('input[id=cliente_cpf]').blur(function () {
        findPessoaByDocumento(this.value);
    });
    $('input[id=cliente_cnpj]').blur(function () {
        findPessoaByDocumento(this.value);
    });

});