'use strict';

import $ from 'jquery';

import routes from '../../../static/fos_js_routes.json';
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

    if ($('input[id=emissao_fiscal_pessoa_id]').val() !== '') {
        let dadosPessoa = $('.DADOSPESSOA');
        dadosPessoa.prop('disabled', true);
    }

    let modalidadeFrete = $('select[id=emissao_fiscal_transp_modalidade_frete]').val();
    let modalidadeFreteLabel = $('select[id=emissao_fiscal_transp_modalidade_frete] option:selected').text();

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

    $('select[id=emissao_fiscal_modalidade_frete]').change(
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
                cep: $('input[id=emissao_fiscal_cep]').val(), //pega valor do campo
                formato: 'json'
            },
            success: function (res) {
                $('input[id=emissao_fiscal_logradouro]').val(res.tipo_logradouro + ' ' + res.logradouro);
                $('input[id=emissao_fiscal_cidade]').val(res.cidade);
                $('input[id=emissao_fiscal_bairro]').val(res.bairro);
                $('select[id=emissao_fiscal_estado]').val(res.uf).change();

            }
        });
    });

    let documentoAtual;

    function findPessoaByDocumento(documento) {

        if (documento === documentoAtual) return;
        documentoAtual = documento;

        $('.DADOSPESSOA').val('').change();
        $('.DADOSPESSOA').prop('disabled', false);

        $.ajax({
            url: Routing.generate('bse_pessoa_findByNome') + '/' + documento.replace(/[^0-9]/g, ""),
            type: 'get',
            dataType: 'json',
            success: function (data) {
                $('input[id=emissao_fiscal_nome]').val(data.nome);
                $('input[id=emissao_fiscal_pessoa_id]').val(data.id);
                $('input[id=emissao_fiscal_razao_social]').val(data.nome);
                $('input[id=emissao_fiscal_nome_fantasia]').val(data.nomeFantasia);
                $('input[id=emissao_fiscal_razao_social]').val(data.razaoSocial);
                $('input[id=emissao_fiscal_inscricao_estadual]').val(data.inscricaoEstadual);

                $('input[id=emissao_fiscal_fone1]').val(data.fone1);
                $('input[id=emissao_fiscal_email]').val(data.email);

                if (data.endereco) {
                    $('input[id=emissao_fiscal_cep]').val(data.endereco.cep);
                    $('input[id=emissao_fiscal_logradouro]').val(data.endereco.logradouro);
                    $('input[id=emissao_fiscal_numero]').val(data.endereco.numero);
                    $('input[id=emissao_fiscal_complemento]').val(data.endereco.complemento);
                    $('input[id=emissao_fiscal_bairro]').val(data.endereco.bairro);
                    $('input[id=emissao_fiscal_cidade]').val(data.endereco.cidade);
                    $('select[id=emissao_fiscal_estado]').val(data.endereco.estado).change();
                }

                if (data.id) {
                    $('.DADOSPESSOA').prop('disabled', true);
                    // Desabilito todos menos o pessoa_id, senão não passa pelo submit
                    $('input[id=emissao_fiscal_pessoa_id]').prop('disabled', false);
                }
            }
        });
    }

    $('input[id=emissao_fiscal_cpf]').blur(function () {
        findPessoaByDocumento(this.value);
    });
    $('input[id=emissao_fiscal_cnpj]').blur(function () {
        findPessoaByDocumento(this.value);
    });


    // Para pesquisar a transportadora pelo seu cnpj

    let cnpjTransportadorAtual;

    $('input[id=emissao_fiscal_transpFornecedor_cnpj]').blur(function () {
        let cnpj = this.value;
        cnpj = cnpj.replace(/[^0-9]/g,'');
        if (!cnpj) return;
        if (cnpj === cnpjTransportadorAtual) return;
        cnpjTransportadorAtual = cnpj;

        $.ajax({
            url: Routing.generate('est_fornecedor_findByDocumento') + '/' + cnpj,
            type: 'get',
            dataType: 'json',
            success: function (data) {
                $('input[id=emissao_fiscal_transpFornecedor_id]').val(data.id);
                $('input[id=emissao_fiscal_transpFornecedor_razaoSocial]').val(data.pessoa.nome);
                $('input[id=emissao_fiscal_transpFornecedor_nomeFantasia]').val(data.pessoa.nomeFantasia);
            }
        });
    });

});