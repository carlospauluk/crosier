<?php

namespace App\Form\Financeiro;

use App\Entity\Base\Pessoa;
use App\Entity\Financeiro\Banco;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MovimentacaoGrupoItemType.
 *
 * Form para movimentações.
 *
 * @package App\Form\Financeiro
 */
class MovimentacaoGrupoItemType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $movimentacao = $event->getData();
            $builder = $event->getForm();

            $builder->add('id', IntegerType::class, array(
                'label' => 'Id',
                'required' => false,
                'disabled' => true
            ));

            $builder->add('tipoLancto', ChoiceType::class, array(
                'label' => 'Tipo Lancto',
                'choices' => ['MOVIMENTAÇÃO AGRUPADA' => 'MOVIMENTACAO_AGRUPADA'],
                'data' => 'MOVIMENTACAO_AGRUPADA'
            ));

            // FIXO: sempre será a pagante do GrupoItem
            $builder->add('categoria', EntityType::class, array(
                'label' => 'Categoria',
                'class' => Categoria::class,
                'choices' => [$this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299])],
                'data' => $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299]),
                'choice_label' => 'descricaoMontadaTree'
            ));

            $builder->add('carteira', EntityType::class, array(
                'label' => 'Carteira',
                'class' => Carteira::class,
                'choices' => [$movimentacao->getGrupoItem()->getCarteiraPagante()],
                'data' => $movimentacao->getGrupoItem()->getCarteiraPagante(),
                'choice_label' => 'descricaoMontada'
            ));

            $builder->add('modo', EntityType::class, array(
                'label' => 'Modo',
                'class' => Modo::class,
                'choices' => [$this->doctrine->getRepository(Modo::class)->find(12)],
                'data' => $this->doctrine->getRepository(Modo::class)->find(12),
                'choice_label' => 'descricaoMontada'
            ));

            $builder->add('categoria', EntityType::class, array(
                'label' => 'Categoria',
                'class' => Categoria::class,
                'choices' => $this->doctrine->getRepository(Categoria::class)->findAll(WhereBuilder::buildOrderBy('codigoOrd')),
                'choice_label' => 'descricaoMontadaTree'
            ));

            $builder->add('centroCusto', EntityType::class, array(
                'class' => CentroCusto::class,
                'choices' => $this->doctrine->getRepository(CentroCusto::class)->findAll(),
                'choice_label' => 'descricaoMontada'
            ));

            $builder->add('status', HiddenType::class, array(
                'data' => 'REALIZADA'
            ));

            $builder->add('dtMoviment', DateType::class, array(
                'label' => 'Dt Moviment',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'crsr-date'
                )
            ));

            $builder->add('dtVencto', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Vencto',
                'attr' => array(
                    'class' => 'crsr-date',
                    'readonly' => true
                ),
                'data' => $movimentacao->getGrupoItem()->getDtVencto()
            ));

            $builder->add('dtVenctoEfetiva', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Vencto Efet',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'data' => $movimentacao->getGrupoItem()->getDtVencto()
            ));

            $builder->add('dtPagto', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Pagto',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'data' => $movimentacao->getGrupoItem()->getDtVencto()
            ));

            $builder->add('dtUtil', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Útil',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'data' => $movimentacao->getGrupoItem()->getDtVencto()
            ));


            $builder->add('pessoa', EntityType::class, array(
                'label' => 'Pessoa',
                'class' => Pessoa::class,
                'choices' => [],
                'choice_label' => 'nome'
            ));

            $builder->add('descricao', TextType::class, array(
                'label' => 'Descrição'
            ));

            $builder->add('documentoBanco', EntityType::class, array(
                'required' => false,
                'label' => 'Banco do Documento',
                'class' => Banco::class,
                'choices' => $this->doctrine->getRepository(Banco::class)
                    ->findAll(),
                'choice_label' => function (Banco $banco) {
                    return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
                }
            ));

            $builder->add('documentoNum', TextType::class, array(
                'label' => 'Núm Documento',
                'required' => false
            ));

            $builder->add('obs', TextareaType::class, array(
                'label' => 'Obs',
                'required' => false
            ));

            $builder->add('valor', MoneyType::class, array(
                'label' => 'Valor',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                )
            ));

            $builder->add('descontos', MoneyType::class, array(
                'required' => false,
                'label' => 'Descontos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                )
            ));

            $builder->add('acrescimos', MoneyType::class, array(
                'required' => false,
                'label' => 'Acréscimos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                )
            ));

            $builder->add('valorTotal', MoneyType::class, array(
                'label' => 'Valor Total',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'disabled' => true
            ));

            // Recorrência

            $builder->add('recorrente', ChoiceType::class, array(
                'label' => 'Recorrente',
                'choices' => array(
                    'SIM' => true,
                    'NÃO' => false
                ),
                'required' => true
            ));

            $builder->add('recorrDia', IntegerType::class, array(
                'label' => 'Dia',
                'attr' => ['min' => 1, 'max' => 31],
                'required' => false
            ));

            $builder->add('recorrTipoRepet', ChoiceType::class, array(
                'label' => 'Tipo Repet',
                'choices' => array(
                    'DIA FIXO' => 'DIA_FIXO',
                    'DIA ÚTIL' => 'DIA_UTIL'
                ),
                'required' => false
            ));

            $builder->add('recorrFrequencia', ChoiceType::class, array(
                'label' => 'Frequência',
                'choices' => array(
                    'MENSAL' => 'MENSAL',
                    'ANUAL' => 'ANUAL'
                ),
                'required' => false
            ));

            $builder->add('recorrVariacao', IntegerType::class, array(
                'label' => 'Variação',
                'required' => false
            ));
        });


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Movimentacao::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'movimentacao';
    }


}