<?php

namespace App\Form\CRM;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ClienteType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Campos gerais (tanto para PESSOA_FISICA quanto para PESSOA_JURIDICA)

        $builder->add('id', HiddenType::class, array(
            'required' => false
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
                'class' => 'TIPO_PESSOA'
            ),
            'required' => true
        ));

        $builder->add('pessoa_id', HiddenType::class, array(
            'required' => false
        ));

        $builder->add('codigo', IntegerType::class, array(
            'required' => false
        ));

        $builder->add('fone1', TextType::class, array(
            'label' => 'Telefone (1)',
            'attr' => array(
                'class' => 'telefone'
            ),
            'required' => false
        ));

        $builder->add('fone2', TextType::class, array(
            'label' => 'Telefone (2)',
            'attr' => array(
                'class' => 'telefone'
            ),
            'required' => false
        ));

        $builder->add('fone3', TextType::class, array(
            'label' => 'Telefone (3)',
            'attr' => array(
                'class' => 'telefone'
            ),
            'required' => false
        ));

        $builder->add('fone4', TextType::class, array(
            'label' => 'Telefone (4)',
            'attr' => array(
                'class' => 'telefone'
            ),
            'required' => false
        ));

        $builder->add('email', TextType::class, array(
            'label' => 'E-mail',
            'attr' => array(
                'class' => 'email'
            ),
            'required' => false
        ));

        $builder->add('obs', TextareaType::class, array(
            'label' => 'Obs',
            'attr' => array(
                'class' => ''
            ),
            'required' => false
        ));


        // Campos PESSOA_FISICA

        $builder->add('cpf', TextType::class, array(
            'label' => 'CPF',
            'attr' => array(
                'class' => 'PESSOA_FISICA cpf'
            ),
            'required' => false
        ));

        $builder->add('nome', TextType::class, array(
            'label' => 'Nome',
            'attr' => array(
                'class' => 'PESSOA_FISICA'
            ),
            'required' => false
        ));

        $builder->add('rg', TextType::class, array(
            'label' => 'RG',
            'attr' => array(
                'class' => 'PESSOA_FISICA'
            ),
            'required' => false
        ));

        $builder->add('dtEmissaoRg', TextType::class, array(
            'label' => 'Dt Emissão RG',
            'attr' => array('class' => 'crsr-datetime PESSOA_FISICA'),
            'required' => false
        ));

        $builder->add('orgaoEmissorRg', TextType::class, array(
            'label' => 'Naturalidade',
            'attr' => array(
                'class' => 'PESSOA_FISICA'
            ),
            'required' => false
        ));

        $builder->add('estadoRg', ChoiceType::class, array(
            'label' => 'Estado RG',
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
                'class' => 'PESSOA_FISICA'
            )
        ));

        $builder->add('sexo', ChoiceType::class, array(
            'label' => 'Sexo',
            'choices' => array(
                'Masculino' => 'MASCULINO',
                'Feminino' => 'FEMININO'
            ),
            'attr' => array(
                'class' => 'PESSOA_FISICA'
            ),
            'required' => false
        ));

        $builder->add('naturalidade', TextType::class, array(
            'label' => 'Naturalidade',
            'attr' => array(
                'class' => 'PESSOA_FISICA'
            ),
            'required' => false
        ));

        $builder->add('estadoCivil', ChoiceType::class, array(
            'label' => 'Estado Civil',
            'choices' => array(
                'Solteiro' => 'SOLTEIRO',
                'Casado' => 'CASADO',
                'Viúvo' => 'VIUVO',
                'Separado' => 'SEPARADO',
                'Divorciado' => 'DIVORCIDADO'
            ),
            'attr' => array(
                'class' => 'PESSOA_FISICA'
            ),
            'required' => false
        ));

        $builder->add('aceitaWhatsapp', ChoiceType::class, array(
            'label' => 'Aceita Whatsapp',
            'choices' => array(
                'Sim' => true,
                'Não' => false
            ),
            'required' => false
        ));

        $builder->add('temWhatsapp', ChoiceType::class, array(
            'label' => 'Tem Whatsapp',
            'choices' => array(
                'Sim' => true,
                'Não' => false
            ),
            'required' => false
        ));


        // Campos PESSOA_JURIDICA

        $builder->add('cnpj', TextType::class, array(
            'label' => 'CNPJ',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA cnpj'
            ),
            'required' => false

        ));

        $builder->add('razaoSocial', TextType::class, array(
            'label' => 'Razão Social',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA'
            ),
            'required' => false
        ));

        $builder->add('nomeFantasia', TextType::class, array(
            'label' => 'Nome Fantasia',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA'
            ),
            'required' => false
        ));

        $builder->add('inscricaoEstadual', TextType::class, array(
            'label' => 'Inscr Est',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA'
            ),
            'required' => false
        ));

        $builder->add('inscricaoMunicipal', TextType::class, array(
            'label' => 'Inscr Munic',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA'
            ),
            'required' => false
        ));


        $builder->add('contato', TextType::class, array(
            'label' => 'Contato',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA'
            ),
            'required' => false
        ));

        $builder->add('website', TextType::class, array(
            'label' => 'Site',
            'attr' => array(
                'class' => 'PESSOA_JURIDICA'
            ),
            'required' => false
        ));


    }

}