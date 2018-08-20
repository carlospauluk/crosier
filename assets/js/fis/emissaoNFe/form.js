'use strict';

import $ from 'jquery';

import routes from '../../fos_js_routes.json';
import Routing from '../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes)


function prepareForm() {
    // quando muda de NFE pra NFCE e vice-versa, esconde os campos
    let tipoFiscal = $(".TIPO_FISCAL").find(':checked').val();
    if (tipoFiscal == 'NFCE') {
        $(".NFE").parent().parent().css('display', 'none');
    } else {
        $(".NFE").parent().parent().css('display', '');
    }

    let tipoPessoa = $(".TIPO_PESSOA").find(':checked').val();

    if (tipoPessoa == 'PESSOA_FISICA') {
        $(".PESSOA_FISICA").parent().parent().css('display', '');
        $(".PESSOA_JURIDICA").parent().parent().css('display', 'none');
    } else {
        $(".PESSOA_FISICA").parent().parent().css('display', 'none');
        $(".PESSOA_JURIDICA").parent().parent().css('display', '');
    }

    if ($('input[id$=_pessoa_id]').val() !== '') {
        let dadosPessoa = $('.DADOSPESSOA');
        dadosPessoa.prop('disabled', true);
    }

    let modalidadeFrete = $('select[id$=_modalidade_frete]').val();
    let modalidadeFreteLabel = $('select[id$=_modalidade_frete] option:selected').text();

    $('#inputModalidadeFrete').val( modalidadeFreteLabel );

    let notaFiscalId = $('.ID_ENTITY').val();
    // quando for uma nova nota fiscal, desabilita todas as abas menos o cabeçalho.
    if (!notaFiscalId) {
        $('.NOTAFISCAL_TAB').addClass('disabled');
        $('#cabecalho-tab').removeClass('disabled');
    }

    if (modalidadeFrete == 'SEM_FRETE') {
        $('#transporte-tab').addClass('disabled');
    } else {
        $('#transporte-tab').removeClass('disabled');
    }

}

$(document).ready(function () {

    $(".TIPO_FISCAL").change(
        function () {
            let dadosPessoa = $('.DADOSPESSOA');
            dadosPessoa.val('');
            dadosPessoa.prop('disabled', false);
            prepareForm();
        });

    $(".TIPO_PESSOA").change(
        function () {
            let dadosPessoa = $('.DADOSPESSOA');
            dadosPessoa.val('');
            dadosPessoa.prop('disabled', false);
            prepareForm();
        });

    $('select[id$=_modalidade_frete]').change(
        function () {
            prepareForm();
        });



    prepareForm();

    $('#pesquisar_cep').click(function () {
        $.ajax({
            url: 'http://cep.republicavirtual.com.br/web_cep.php',
            type: 'get',
            dataType: 'json',
            crossDomain: true,
            data: {
                cep: $('input[id$=_cep]').val(), //pega valor do campo
                formato: 'json'
            },
            success: function (res) {
                $('input[id$=_logradouro]').val(res.tipo_logradouro + ' ' + res.logradouro);
                $('input[id$=_cidade]').val(res.cidade);
                $('input[id$=_bairro]').val(res.bairro);
                $('select[id$=_estado]').val(res.uf).change();

            }
        });
    });

    let documentoAtual;

    function findPessoaByDocumento(documento) {

        if (documento === documentoAtual) return;
        documentoAtual = documento;

        $.ajax({
            url: Routing.generate('bse_pessoa_findByNome') + '/' + documento.replace(/[^0-9]/g, ""),
            type: 'get',
            dataType: 'json',
            success: function (data) {
                $('input[id$=_nome]').val(data.nome);
                $('input[id$=_pessoa_id]').val(data.id);
                $('input[id$=_razao_social]').val(data.nome);
                $('input[id$=_nome_fantasia]').val(data.nomeFantasia);
                $('input[id$=_razao_social]').val(data.razaoSocial);

                $('input[id$=_fone1]').val(data.fone1);
                $('input[id$=_email]').val(data.email);

                if (data.endereco) {
                    $('input[id$=_cep]').val(data.endereco.cep);
                    $('input[id$=_logradouro]').val(data.endereco.logradouro);
                    $('input[id$=_numero]').val(data.endereco.numero);
                    $('input[id$=_complemento]').val(data.endereco.complemento);
                    $('input[id$=_bairro]').val(data.endereco.bairro);
                    $('input[id$=_cidade]').val(data.endereco.cidade);
                    $('select[id$=_estado]').val(data.endereco.estado).change();
                }

                if (data.id) {
                    $('.DADOSPESSOA').prop('disabled', true);
                }
            }
        });
    }

    $('input[id$=_cpf]').blur(function () {
        findPessoaByDocumento(this.value);
    });
    $('input[id$=_cnpj]').blur(function () {
        findPessoaByDocumento(this.value);
    });

});