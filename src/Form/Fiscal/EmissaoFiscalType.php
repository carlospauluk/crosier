<?php

namespace App\Form\Fiscal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Form que serve tanto para emissão fiscal por PV quanto para NFe.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EmissaoFiscalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $builder = $event->getForm();

            $disabled = false;
            if ($data and array_key_exists('permiteFaturamento', $data)) {
                $disabled = $data['permiteFaturamento'] == false;
            }

            $builder->add('nota_fiscal_id', HiddenType::class, array(
                'required' => false,
                // atributo utilizado para que o javascript possa localizar facilmente este input
                'attr' => array(
                    'class' => 'ID_ENTITY'
                )
            ));

            $builder->add('_info_status', TextType::class, array(
                'label' => 'Status',
                'required' => false,
                'disabled' => true
            ));

            $builder->add('tipo', ChoiceType::class, array(
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choices' => array(
                    'Nota Fiscal' => 'NFE',
                    'Cupom Fiscal' => 'NFCE'
                ),
                'attr' => array(
                    'class' => 'TIPO_FISCAL'
                ),
                'disabled' => $disabled or $data['nota_fiscal_id'] !== null
            ));

            $builder->add('tipoPessoa', ChoiceType::class, array(
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choices' => array(
                    'CPF' => 'PESSOA_FISICA',
                    'CNPJ' => 'PESSOA_JURIDICA'
                ),
                'attr' => array(
                    'class' => 'TIPO_PESSOA DADOSPESSOA'
                ),
                'disabled' => $disabled
            ));

            $builder->add('pessoa_id', HiddenType::class, array(
                'required' => false,
                'disabled' => $disabled,
                'attr' => array(
                    'class' => 'DADOSPESSOA'
                )
            ));

            $builder->add('numero_nf', IntegerType::class, array(
                'label' => 'Número NF',
                'required' => false,
                'disabled' => true
            ));
            $builder->add('serie', IntegerType::class, array(
                'label' => 'Série NF',
                'required' => false,
                'disabled' => true
            ));
            $builder->add('uuid', TextType::class, array(
                'label' => 'UUID',
                'required' => false,
                'disabled' => true
            ));

            $builder->add('dtEmissao', TextType::class, array(
                'label' => 'Dt Emissão',
                'attr' => array('class' => 'crsr-datetime'),
                'required' => false,
                'disabled' => true
            ));

            $builder->add('dtNascimento', TextType::class, array(
                'label' => 'Dt Nascimento',
                'attr' => array('class' => 'crsr-datetime'),
                'required' => false
            ));

            $builder->add('dtSaiEnt', TextType::class, array(
                'label' => 'Dt Saída/Entrada',
                'attr' => array('class' => 'crsr-datetime'),
                'required' => true,
                'disabled' => $disabled
            ));

            $builder->add('subtotal', MoneyType::class, array(
                'label' => 'Subtotal',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false,
                'disabled' => true
            ));

            $builder->add('totalDescontos', MoneyType::class, array(
                'label' => 'Descontos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false,
                'disabled' => true
            ));

            $builder->add('valorTotal', MoneyType::class, array(
                'label' => 'Valor Total',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false,
                'disabled' => true
            ));


            $builder->add('cpf', TextType::class, array(
                'label' => 'CPF',
                'attr' => array(
                    'class' => 'PESSOA_FISICA cpf'
                ),
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('nome', TextType::class, array(
                'label' => 'Nome',
                'attr' => array(
                    'class' => 'PESSOA_FISICA DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('cnpj', TextType::class, array(
                'label' => 'CNPJ',
                'attr' => array(
                    'class' => 'PESSOA_JURIDICA cnpj'
                ),
                'required' => false,
                'disabled' => $disabled

            ));
            // Adiciona campo RAZAO SOCIAL (bon_pessoa.nome)
            $builder->add('razaoSocial', TextType::class, array(
                'label' => 'Razão Social',
                'attr' => array(
                    'class' => 'PESSOA_JURIDICA DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            // Adiciona campo NOME FANTASIA
            $builder->add('nomeFantasia', TextType::class, array(
                'label' => 'Nome Fantasia',
                'attr' => array(
                    'class' => 'PESSOA_JURIDICA DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('inscricaoEstadual', TextType::class, array(
                'label' => 'Inscr Estadual',
                'attr' => array(
                    'class' => 'PESSOA_JURIDICA'
                ),
                'required' => false,
                'disabled' => $disabled

            ));

            $builder->add('fone1', TextType::class, array(
                'label' => 'Telefone',
                'attr' => array(
                    'class' => 'NFE telefone DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            $builder->add('email', TextType::class, array(
                'label' => 'E-mail',
                'attr' => array(
                    'class' => 'NFE email DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('cep', TextType::class, array(
                'label' => 'CEP',
                'attr' => array(
                    'class' => 'NFE cep DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('logradouro', TextType::class, array(
                'label' => 'Logradouro',
                'attr' => array(
                    'class' => 'NFE DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            $builder->add('numero', TextType::class, array(
                'label' => 'Número',
                'attr' => array(
                    'class' => 'NFE DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            $builder->add('complemento', TextType::class, array(
                'label' => 'Complemento',
                'attr' => array(
                    'class' => 'NFE DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            $builder->add('bairro', TextType::class, array(
                'label' => 'Bairro',
                'attr' => array(
                    'class' => 'NFE DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            $builder->add('cidade', TextType::class, array(
                'label' => 'Cidade',
                'attr' => array(
                    'class' => 'NFE DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('estado', ChoiceType::class, array(
                'label' => 'Estado',
                'choices' => array(
                    'Acre' => 'AC',
                    'Alagoas' => 'AL',
                    'Amapá' => 'AP',
                    'Amazonas' => 'AM',
                    'Bahia' => 'BA',
                    'Ceará' => 'CE',
                    'Distrito Federal' => 'DF',
                    'Espírito Santo' => 'ES',
                    'Goiás' => 'GO',
                    'Maranhão' => 'MA',
                    'Mato Grosso' => 'MT',
                    'Mato Grosso do Sul' => 'MS',
                    'Minas Gerais' => 'MG',
                    'Pará' => 'PA',
                    'Paraíba' => 'PB',
                    'Paraná' => 'PR',
                    'Pernambuco' => 'PE',
                    'Piauí' => 'PI',
                    'Rio de Janeiro' => 'RJ',
                    'Rio Grande do Norte' => 'RN',
                    'Rio Grande do Sul' => 'RS',
                    'Rondônia' => 'RO',
                    'Roraima' => 'RR',
                    'Santa Catarina' => 'SC',
                    'São Paulo' => 'SP',
                    'Sergipe' => 'SE',
                    'Tocantins' => 'TO'
                ),
                'required' => false,
                'attr' => array(
                    'class' => 'NFE DADOSPESSOA'
                ),
                'disabled' => $disabled
            ));

            $builder->add('cancelamento_motivo', TextType::class, array(
                'label' => 'Motivo do Cancelamento',
                'required' => true,
                'constraints' => array(
                    new Length(array(
                        'min' => 15,
                        'max' => 255
                    ))
                )
            ));

            $builder->add('carta_correcao', TextType::class, array(
                'label' => 'Carta de Correção',
                'required' => true,
                'constraints' => array(
                    new Length(array(
                        'min' => 15,
                        'max' => 1000
                    ))
                )
            ));

            $builder->add('natureza_operacao', TextType::class, array(
                'label' => 'Natureza da Operação',
                'required' => true,
                'disabled' => $disabled
            ));

            $builder->add('entrada', ChoiceType::class, array(
                'label' => 'Entrada/Saída',
                'required' => true,
                'choices' => array(
                    'Entrada' => true,
                    'Saída' => false
                ),
                'disabled' => $disabled
            ));

            // Campos para FRETE

            $builder->add('transp_modalidade_frete', ChoiceType::class, array(
                'label' => 'Modalidade Frete',
                'required' => true,
                'choices' => array(
                    'Sem frete' => 'SEM_FRETE',
                    'Por conta do emitente' => 'EMITENTE',
                    'Por conta do destinatário/remetente' => 'DESTINATARIO',
                    'Por conta de terceiros' => 'TERCEIROS'
                ),
                'disabled' => $disabled
            ));

            $builder->add('transpEspecieVolumes', TextType::class, array(
                'label' => 'Espécie Volumes',
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('transpMarcaVolumes', TextType::class, array(
                'label' => 'Marca Volumes',
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('transpNumeracaoVolumes', TextType::class, array(
                'label' => 'Marca Volumes',
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('transpPesoBruto', NumberType::class, array(
                'label' => 'Peso Bruto',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => false,
                'disabled' => $disabled,
                'help' => 'Em kg',
                'empty_data' => ''
            ));

            $builder->add('transpPesoLiquido', NumberType::class, array(
                'label' => 'Peso Líquido',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => false,
                'disabled' => $disabled,
                'help' => 'Em kg'
            ));

            $builder->add('transpQtdeVolumes', IntegerType::class, array(
                'label' => 'Qtde Volumes',
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('transpFornecedor_id', HiddenType::class, array(
                'required' => false,
                'disabled' => $disabled
            ));

            $builder->add('transpFornecedor_cnpj', TextType::class, array(
                'label' => 'CNPJ da Transportadora',
                'attr' => array(
                    'class' => 'cnpj'
                ),
                'required' => false,
                'disabled' => $disabled

            ));
            // Adiciona campo RAZAO SOCIAL (bon_pessoa.nome)
            $builder->add('transpFornecedor_razaoSocial', TextType::class, array(
                'label' => 'Razão Social',
                'required' => false,
                'disabled' => true
            ));
            // Adiciona campo NOME FANTASIA
            $builder->add('transpFornecedor_nomeFantasia', TextType::class, array(
                'label' => 'Nome Fantasia',
                'required' => false,
                'disabled' => true
            ));


            $builder->add('indicador_forma_pagto', ChoiceType::class, array(
                'label' => 'Forma Pagto',
                'required' => true,
                'choices' => array(
                    'A vista' => 'VISTA',
                    'A prazo' => 'PRAZO',
                    'Outros' => 'OUTROS'
                ),
                'disabled' => $disabled
            ));

            $builder->add('indicador_forma_pagto', ChoiceType::class, array(
                'label' => 'Forma Pagto',
                'required' => true,
                'choices' => array(
                    'A vista' => 'VISTA',
                    'A prazo' => 'PRAZO',
                    'Outros' => 'OUTROS'
                ),
                'disabled' => $disabled
            ));

            $builder->add('finalidade_nf', ChoiceType::class, array(
                'label' => 'Finalidade',
                'required' => true,
                'choices' => array(
                    'Normal' => 'NORMAL',
                    'Devolução' => 'DEVOLUCAO',
                    'Ajuste' => 'AJUSTE',
                    'Complementar' => 'COMPLEMENTAR'
                ),
                'disabled' => $disabled
            ));

            $builder->add('info_compl', TextareaType::class, array(
                'label' => 'Inform Complement',
                'required' => false,
                'attr' => array(
                    'rows' => '5'
                ),
                'disabled' => $disabled
            ));

            $builder->add('a03idNfReferenciada', TextType::class, array(
                'label' => 'Id NF Referenciada',
                'required' => false,
                'disabled' => $disabled
            ));
        });
    }

}