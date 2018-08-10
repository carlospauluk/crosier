<?php
namespace App\Form\Fiscal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

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
                'disabled' => $disabled
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
                'disabled' => $disabled
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
            
            $builder->add('dtEmissao', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:ii:ss',
                'label' => 'Dt Emissão',
                'attr' => array('class' => 'crsr-date'),
                'required' => true,
                'disabled' => $disabled
            ));
            
            $builder->add('dtSaiEnt', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:ii:ss',
                'label' => 'Dt Saída/Entrada',
                'attr' => array('class' => 'crsr-date'),
                'required' => true,
                'disabled' => $disabled
            ));
            
            $builder->add('valorTotal', MoneyType::class, array(
                'label' => 'Limite',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                )
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
            $builder->add('razao_social', TextType::class, array(
                'label' => 'Razão Social',
                'attr' => array(
                    'class' => 'PESSOA_JURIDICA DADOSPESSOA'
                ),
                'required' => false,
                'disabled' => $disabled
            ));
            // Adiciona campo NOME FANTASIA
            $builder->add('nome_fantasia', TextType::class, array(
                'label' => 'Nome Fantasia',
                'attr' => array(
                    'class' => 'PESSOA_JURIDICA DADOSPESSOA'
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
            
            $builder->add('entrada_saida', ChoiceType::class, array(
                'label' => '',
                'required' => true,
                'choices' => array(
                    'Entrada' => true,
                    'Saída' => false
                ),
                'disabled' => $disabled
            ));
            
            
            $builder->add('transp_modalidade_frete', ChoiceType::class, array(
                'label' => 'Modalidade Frete',
                'required' => true,                
                'choices' => array(
                    'Sem frete' => 'SEM_FRETE',
                    'Por conta do emitente' => 'EMITENTE',
                    'Por conta do destinatário' => 'DESTINATARIO'
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
        });
    }
    
    // public function configureOptions(OptionsResolver $resolver)
    // {
    // $resolver->setDefaults(array(
    // 'data_class' => NotaFiscal::class
    // ));
    // }
}