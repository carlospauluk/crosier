<?php
namespace App\Form\Fiscal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EmissaoFiscalPVType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('tipo', ChoiceType::class, array(
            'expanded' => true,
            'label_attr' => ['class' => 'radio-inline'],
            'choices' => array(
                'Nota Fiscal' => 'NFE',
                'Cupom Fiscal' => 'NFCE'
            ),
            'attr' => array('class' => 'TIPO_FISCAL')
        ));
        
        $builder->add('tipoPessoa', ChoiceType::class, array(
            'expanded' => true,
            'label_attr' => ['class' => 'radio-inline'],
            'choices' => array(
                'CPF' => 'PESSOA_FISICA',
                'CNPJ' => 'PESSOA_JURIDICA'
            ),
            'attr' => array('class' => 'TIPO_PESSOA')
        ));
        
        $builder->add('pessoa_id', HiddenType::class, array(
            'required' => false
        ));
        
        // Adiciona campo CPF
        $builder->add('cpf', TextType::class, array(
            'label' => 'CPF',
            'attr' => array('class' => 'PESSOA_FISICA cpf'),
            'required' => false
        ));
        // Adiciona campo NOME
        $builder->add('nome', TextType::class, array(
            'label' => 'Nome',
            'attr' => array('class' => 'PESSOA_FISICA'),
            'required' => false
        ));
        
        $builder->add('cnpj', TextType::class, array(
            'label' => 'CNPJ',
            'attr' => array('class' => 'PESSOA_JURIDICA cnpj'),
            'required' => false
            
        ));
        // Adiciona campo RAZAO SOCIAL (bon_pessoa.nome)
        $builder->add('razao_social', TextType::class, array(
            'label' => 'Razão Social',
            'attr' => array('class' => 'PESSOA_JURIDICA'),
            'required' => false
        ));
        // Adiciona campo NOME FANTASIA
        $builder->add('nome_fantasia', TextType::class, array(
            'label' => 'Nome Fantasia',
            'attr' => array('class' => 'PESSOA_JURIDICA'),
            'required' => false
        ));
        
        $builder->add('fone1', TextType::class, array(
            'label' => 'Telefone',
            'attr' => array('class' => 'NFE telefone'),
            'required' => false
        ));
        $builder->add('email', TextType::class, array(
            'label' => 'E-mail',
            'attr' => array('class' => 'NFE email'),
            'required' => false
        ));
        
        $builder->add('cep', TextType::class, array(
            'label' => 'CEP',
            'attr' => array('class' => 'NFE cep'),
            'required' => false
        ));
        
        $builder->add('logradouro', TextType::class, array(
            'label' => 'Logradouro',
            'attr' => array('class' => 'NFE'),
            'required' => false
        ));
        $builder->add('numero', TextType::class, array(
            'label' => 'Número',
            'attr' => array('class' => 'NFE'),
            'required' => false
        ));
        $builder->add('complemento', TextType::class, array(
            'label' => 'Complemento',
            'attr' => array('class' => 'NFE'),
            'required' => false
        ));
        $builder->add('bairro', TextType::class, array(
            'label' => 'Bairro',
            'attr' => array('class' => 'NFE'),
            'required' => false
        ));
        $builder->add('cidade', TextType::class, array(
            'label' => 'Cidade',
            'attr' => array('class' => 'NFE'),
            'required' => false
        ));
        
        $builder->add('estado', ChoiceType::class, array(
            'label' => 'Estado',
            'choices' => array(
                'Acre'=>'AC',
                'Alagoas'=>'AL',
                'Amapá'=>'AP',
                'Amazonas'=>'AM',
                'Bahia'=>'BA',
                'Ceará'=>'CE',
                'Distrito Federal'=>'DF',
                'Espírito Santo'=>'ES',
                'Goiás'=>'GO',
                'Maranhão'=>'MA',
                'Mato Grosso'=>'MT',
                'Mato Grosso do Sul'=>'MS',
                'Minas Gerais'=>'MG',
                'Pará'=>'PA',
                'Paraíba'=>'PB',
                'Paraná'=>'PR',
                'Pernambuco'=>'PE',
                'Piauí'=>'PI',
                'Rio de Janeiro'=>'RJ',
                'Rio Grande do Norte'=>'RN',
                'Rio Grande do Sul'=>'RS',
                'Rondônia'=>'RO',
                'Roraima'=>'RR',
                'Santa Catarina'=>'SC',
                'São Paulo'=>'SP',
                'Sergipe'=>'SE',
                'Tocantins'=>'TO'
            ), 
            'required' => false,
            'attr' => array('class' => 'NFE')
        ));
        
        
        
        
    }

    
//     public function configureOptions(OptionsResolver $resolver)
//     {
//         $resolver->setDefaults(array(
//             'data_class' => NotaFiscal::class
//         ));
//     }
}