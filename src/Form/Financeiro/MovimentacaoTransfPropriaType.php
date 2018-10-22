<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MovimentacaoTransfPropriaType.
 *
 * Form para movimentações.
 *
 * @package App\Form\Financeiro
 */
class MovimentacaoTransfPropriaType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', HiddenType::class, array(
            'data' => 'REALIZADA'
        ));

        $builder->add('centroCusto', EntityType::class, array(
            'class' => CentroCusto::class,
            'choices' => $this->doctrine->getRepository(CentroCusto::class)->findAll(),
            'choice_label' => 'descricaoMontada'
        ));

        $builder->add('categoria', EntityType::class, array(
            'label' => 'Categoria',
            'class' => Categoria::class,
            'choices' => [$this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299])],
            'data' => $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299]),
            'choice_label' => 'descricaoMontadaTree'
        ));

        $builder->add('tipoLancto', HiddenType::class, array(
            'label' => 'Tipo Lancto',
            'data' => 'TRANSF_PROPRIA'
        ));

        $builder->add('carteira', EntityType::class, array(
            'label' => 'Origem',
            'class' => Carteira::class,
            'choices' => array_merge([null], $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo'))),
            'choice_label' => function ($carteira) {
                if ($carteira) {
                    return $carteira->getDescricaoMontada();
                }
            }
        ));

        $builder->add('carteiraDestino', EntityType::class, array(
            'label' => 'Destino',
            'class' => Carteira::class,
            'choices' => array_merge([null], $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo'))),
            'choice_label' => function ($carteira) {
                if ($carteira) {
                    return $carteira->getDescricaoMontada();
                }
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

        $builder->add('dtUtil', DateType::class, array(
            'widget' => 'single_text',
            'required' => false,
            'format' => 'dd/MM/yyyy',
            'label' => 'Dt Útil',
            'attr' => array(
                'class' => 'crsr-date'
            )
        ));

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
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

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Movimentacao::class
        ));
    }
}