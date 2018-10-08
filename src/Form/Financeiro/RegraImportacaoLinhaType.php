<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\Banco;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\RegraImportacaoLinha;
use App\Entity\Financeiro\Status;
use App\Entity\Financeiro\TipoLancto;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegraImportacaoLinhaType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('regraRegexJava', TextType::class, array(
            'label' => 'Regex Java'
        ));

        $builder->add('tipoLancto', ChoiceType::class, array(
            'label' => 'Tipo de Lancto',
            'choices' => TipoLancto::getChoices()
        ));

        $builder->add('status', ChoiceType::class, array(
            'choices' => Status::getChoices()
        ));

        $builder->add('carteira', EntityType::class, array(
            'label' => 'Carteira',
            'class' => Carteira
            ::class,
            'choices' => array_merge([null], $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo'))),
            'choice_label' => function (?Carteira $carteira) {
                if ($carteira) {
                    return $carteira->getCodigo() . " - " . $carteira->getDescricao();
                }
            },
            'required' => false
        ));

        $builder->add('carteiraDestino', EntityType::class, array(
            'label' => 'Carteira Destino',
            'class' => Carteira::class,
            'choices' => array_merge([null], $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo'))),
            'choice_label' => function (?Carteira $carteira) {
                if ($carteira) {
                    return $carteira->getCodigo() . " - " . $carteira->getDescricao();
                }
            },
            'required' => false
        ));

        $builder->add('centroCusto', EntityType::class, array(
            'class' => CentroCusto::class,
            'choices' => $this->doctrine->getRepository(CentroCusto::class)->findAll(WhereBuilder::buildOrderBy('codigo')),
            'choice_label' => function (CentroCusto $centroCusto) {
                return $centroCusto->getDescricao();
            }
        ));

        $builder->add('modo', EntityType::class, array(
            'class' => Modo::class,
            'choices' => $this->doctrine->getRepository(Modo::class)->findAll(WhereBuilder::buildOrderBy('codigo')),
            'choice_label' => function (Modo $modo) {
                return $modo->getCodigo() . " - " . $modo->getDescricao();
            }
        ));

        $builder->add('padraoDescricao', TextType::class, array(
            'label' => 'Padrão da Descrição',
            'attr' => ['style' => 'text-transform: none;'],
            'data' => '%s'
        ));

        $builder->add('categoria', EntityType::class, array(
            'class' => Categoria::class,
            'choice_label' => 'descricaoMontada',
            'attr' => ['data-route' => 'fin_categoria_select2json',
                'class' => 'autoSelect2']
        ));

        $builder->add('sinalValor', ChoiceType::class, array(
            'choices' => array(
                'Ambos' => 0,
                'Positivo' => 1,
                'Negativo' => -1
            )
        ));

        $builder->add('chequeBanco', EntityType::class, array(
            'required' => false,
            'label' => 'Cheque - Banco',
            'class' => Banco::class,
            'choices' => $this->doctrine->getRepository(Banco::class)
                ->findAll(),
            'choice_label' => function (Banco $banco) {
                return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
            }
        ));

        $builder->add('chequeAgencia', TextType::class, array(
            'label' => 'Cheque - Agência',
            'required' => false
        ));

        $builder->add('chequeConta', TextType::class, array(
            'label' => 'Cheque - Conta',
            'required' => false
        ));

        $builder->add('chequeNumCheque', TextType::class, array(
            'label' => 'Cheque - Número',
            'required' => false
        ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RegraImportacaoLinha::class
        ));
    }
}