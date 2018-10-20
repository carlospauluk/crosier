<?php

namespace App\Form\Financeiro;

use App\Entity\Base\Pessoa;
use App\Entity\Financeiro\Banco;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Form\ChoiceLoader;
use App\Utils\Repository\WhereBuilder;
use IntlDateFormatter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MovimentacaoType.
 *
 * Form para movimentações.
 *
 * @package App\Form\Financeiro
 */
class MovimentacaoType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('id', IntegerType::class, array(
            'label' => 'Id',
            'required' => false,
            'disabled' => true
        ));

        $builder->add('tipoLancto', ChoiceType::class, array(
            'label' => 'Tipo Lancto',
            'choices' => array(
                'MOVIMENTAÇÃO' => 'GERAL',
                'CHEQUE PRÓPRIO' => 'CHEQUE_PROPRIO',
                'CHEQUE TERCEIROS' => 'CHEQUE_TERCEIROS'
            )
        ));

        $builder->add('carteira', EntityType::class, array(
            'label' => 'Carteira',
            'class' => Carteira::class,
            'choices' => $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo')),
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getDescricaoMontada();
            }
        ));

        $repoModo = $this->doctrine->getRepository(Modo::class);
        $modos = $repoModo->findAll(WhereBuilder::buildOrderBy('codigo'));
        $builder->add('modo', EntityType::class, array(
            'label' => 'Modo',
            'class' => Modo::class,
            'choices' => $modos,
            'choice_label' => function (Modo $modo) {
                return $modo->getDescricaoMontada();
            }
        ));

        $categorias = $this->doctrine->getRepository(Categoria::class)->findAll(WhereBuilder::buildOrderBy('codigoOrd'));
        $builder->add('categoria', EntityType::class, array(
            'label' => 'Categoria',
            'class' => Categoria::class,
            'choices' => $categorias,
            'choice_label' => 'descricaoMontadaTree'
        ));

        $repo = $this->doctrine->getRepository(CentroCusto::class);
        $centrosCusto = $repo->findAll();
        $builder->add('centroCusto', EntityType::class, array(
            'class' => CentroCusto::class,
            'choices' => $centrosCusto,
            'choice_label' => 'descricaoMontada'
        ));

        $builder->add('status', HiddenType::class, array(
            'data' => 'A_PAGAR_RECEBER'
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
                'class' => 'crsr-date'
            )
        ));

        $builder->add('dtVenctoEfetiva', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'label' => 'Dt Vencto Efet',
            'attr' => array(
                'class' => 'crsr-date'
            ),
            'required' => false

        ));

        $builder->add('dtPagto', DateType::class, array(
            'widget' => 'single_text',
            'required' => false,
            'format' => 'dd/MM/yyyy',
            'label' => 'Dt Pagto',
            'attr' => array(
                'class' => 'crsr-date'
            )
        ));

        // Adiciono o dtUtil mas como Hidden para evitar os erros do @Assert/NotNull do Doctrine
        $builder->add(
            $builder->create('dtUtil', HiddenType::class, array(
                'data' => new \DateTime(),
                'data_class' => null
            ))->addViewTransformer(
                new DateTimeToLocalizedStringTransformer(
                    null,
                    null,
                    null,
                    null,
                    IntlDateFormatter::GREGORIAN,
                    'dd/MM/yyyy')));


        $builder->add('pessoa', ChoiceType::class, array(
            'choice_loader' => new ChoiceLoader($builder, $this->doctrine->getRepository(Pessoa::class), 'getPessoa', function ($pessoa) {
                return is_object($pessoa) ? $pessoa->getNome() : null;
            })
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
            'required' => false,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));

        $builder->add('acrescimos', MoneyType::class, array(
            'required' => false,
            'label' => 'Acréscimos',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => false,
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

        // Cheque

        $builder->add('chequeBanco', EntityType::class, array(
            'required' => false,
            'label' => 'Banco',
            'class' => Banco::class,
            'choices' => $this->doctrine->getRepository(Banco::class)
                ->findAll(),
            'choice_label' => function (Banco $banco) {
                return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
            },
            'attr' => array(
                'class' => 'CAMPOS_CHEQUE'
            )
        ));

        $builder->add('chequeAgencia', TextType::class, array(
            'label' => 'Agência',
            'required' => false,
            'attr' => array(
                'class' => 'CAMPOS_CHEQUE'
            )
        ));

        $builder->add('chequeConta', TextType::class, array(
            'label' => 'Conta',
            'required' => false,
            'attr' => array(
                'class' => 'CAMPOS_CHEQUE'
            )
        ));

        $builder->add('chequeNumCheque', TextType::class, array(
            'label' => 'Núm Cheque',
            'required' => false,
            'attr' => array(
                'class' => 'CAMPOS_CHEQUE'
            )
        ));


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Movimentacao::class
        ));
    }
}