<?php

namespace App\Form\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Financeiro\Banco;
use App\Entity\Financeiro\BandeiraCartao;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\GrupoItem;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\OperadoraCartao;
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
 * Class MovimentacaoType.
 *
 * Form para movimentações.
 *
 * @package App\Form\Financeiro
 */
class MovimentacaoType extends AbstractType
{

    private $doctrine;

    private $movimentacaoBusiness;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getMovimentacaoBusiness(): MovimentacaoBusiness
    {
        return $this->movimentacaoBusiness;
    }

    /**
     * @required
     * @param mixed $movimentacaoBusiness
     */
    public function setMovimentacaoBusiness(MovimentacaoBusiness $movimentacaoBusiness): void
    {
        $this->movimentacaoBusiness = $movimentacaoBusiness;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $movimentacao = $event->getData();
            $builder = $event->getForm();

            $builder->add('id', IntegerType::class, array(
                'label' => 'Id',
                'disabled' => true,
                'required' => false
            ));

            // Adiciono este por default, sabendo que será alterado no beforeSave
            $builder->add('status', HiddenType::class, array(
                'data' => 'A_PAGAR_RECEBER',
                'required' => false
            ));

            // Já retorna os valores dentro das regras
            // FIXME: transformar tipoLancto em entidade
            $choices = null;
            if ($movimentacao and $movimentacao->getTipoLancto()) {
                $tiposLanctos = $this->getMovimentacaoBusiness()->getTiposLanctos();
                if (isset($tiposLanctos[$movimentacao->getTipoLancto()])) {
                    $tipoLancto = $tiposLanctos[$movimentacao->getTipoLancto()];
                    $choices = [$tipoLancto['title'] => $movimentacao->getTipoLancto()];
                }
            } else {
                $choices = [
                    'GERAL' => 'GERAL',
                    'TRANSFERÊNCIA PRÓPRIA' => 'TRANSF_PROPRIA',
                    'PARCELAMENTO' => 'PARCELAMENTO',
                    'MOVIMENTAÇÃO DE GRUPO' => 'GRUPO'
                ];
            }
            $params = [
                'label' => 'Tipo Lancto',
                'attr' => ['data-val' => (null !== $movimentacao) ? $movimentacao->getTipoLancto() : ''],
                'required' => false
            ];
            if ($choices) {
                $params['choices'] = $choices;
            }
            $builder->add('tipoLancto', ChoiceType::class, $params);

            // Para que o campo select seja montado já com o valor selecionado (no $('#movimentacao_carteira').val())
            $builder->add('carteira', EntityType::class, array(
                'label' => 'Carteira',
                'class' => Carteira::class,
                'choice_label' => function (?Carteira $carteira) {
                    return $carteira ? $carteira->getDescricaoMontada() : null;
                },
                'choices' => [null,$this->doctrine->getRepository(Carteira::class)->findAll()],
                'data' => (null !== $movimentacao and null !== $movimentacao->getCarteira()) ? $movimentacao->getCarteira() : null,
                'attr' => ['data-val' => (null !== $movimentacao and null !== $movimentacao->getCarteira() and null !== $movimentacao->getCarteira()->getId()) ? $movimentacao->getCarteira()->getId() : ''],
                'required' => false
            ));

            // Também passo o data-valpai para poder selecionar o valor no campo 'grupo' que é somente um auxiliar na tela
            $builder->add('grupoItem', EntityType::class, array(
                'label' => 'Grupo Item',
                'class' => GrupoItem::class,
                'choice_label' => 'descricao',
                'choices' => null,
                'data' => (null !== $movimentacao and null !== $movimentacao->getGrupoItem()) ? $movimentacao->getGrupoItem() : null,
                'attr' => [
                    'data-val' => (null !== $movimentacao and null !== $movimentacao->getGrupoItem() and null !== $movimentacao->getGrupoItem()->getId()) ? $movimentacao->getGrupoItem()->getId() : '',
                    'data-valpai' => (null !== $movimentacao and null !== $movimentacao->getGrupoItem() and null !== $movimentacao->getGrupoItem()->getPai()) ? $movimentacao->getGrupoItem()->getPai()->getId() : ''
                ],
                'required' => false
            ));


            $builder->add('categoria', EntityType::class, array(
                'label' => 'Categoria',
                'class' => Categoria::class,
                'choice_label' => 'descricaoMontadaTree',
                'choices' => null,
                'data' => (null !== $movimentacao and null !== $movimentacao->getCategoria()) ? $movimentacao->getCategoria() : null,
                'attr' => ['data-val' => (null !== $movimentacao and null !== $movimentacao->getCategoria() and null !== $movimentacao->getCategoria()->getId()) ? $movimentacao->getCategoria()->getId() : ''],
                'required' => false
            ));


            // só é obrigatório nos casos de tipoLancto = 'TRANSF_PROPRIA'
            $builder->add('carteiraDestino', EntityType::class, array(
                'label' => 'Destino',
                'class' => Carteira::class,
                'choice_label' => 'descricaoMontada',
                'choices' => null,
                'attr' => ['data-val' => (null !== $movimentacao and null !== $movimentacao->getCarteiraDestino()) ? $movimentacao->getCarteiraDestino()->getId() : ''],
                'required' => false
            ));

            $builder->add('modo', EntityType::class, array(
                'label' => 'Modo',
                'class' => Modo::class,
                'choices' => array_merge([null],$this->doctrine->getRepository(Modo::class)->findAll(WhereBuilder::buildOrderBy('codigo'))),
                'choice_label' => function (?Modo $modo) {
                    return $modo ? $modo->getDescricaoMontada() : null;
                },
                'required' => false
            ));

            $builder->add('bandeiraCartao', EntityType::class, array(
                'label' => 'Bandeira',
                'class' => BandeiraCartao::class,
                'choices' => array_merge([null], $this->doctrine->getRepository(BandeiraCartao::class)->findAll(WhereBuilder::buildOrderBy('descricao'))),
                'choice_label' => function (?BandeiraCartao $bandeiraCartao) {
                    return $bandeiraCartao ? $bandeiraCartao->getDescricao() : '';
                },
                'required' => false
            ));

            $builder->add('operadoraCartao', EntityType::class, array(
                'label' => 'Operadora',
                'class' => OperadoraCartao::class,
                'choices' => array_merge([null], $this->doctrine->getRepository(OperadoraCartao::class)->findAll(WhereBuilder::buildOrderBy('descricao'))),
                'choice_label' => function (?OperadoraCartao $operadoraCartao) {
                    return $operadoraCartao ? $operadoraCartao->getDescricao() : '';
                },
                'required' => false
            ));

            $builder->add('centroCusto', EntityType::class, array(
                'class' => CentroCusto::class,
                'choices' => $this->doctrine->getRepository(CentroCusto::class)->findAll(),
                'choice_label' => 'descricaoMontada',
                'required' => false
            ));


            $builder->add('dtMoviment', DateType::class, array(
                'label' => 'Dt Moviment',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'required' => false
            ));

            $builder->add('dtVencto', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Vencto',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'required' => false
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
                ),
                'required' => false
            ));

            $builder->add('pessoa', EntityType::class, array(
                'label' => 'Pessoa',
                'class' => Pessoa::class,
                'choices' => [null],
                'choice_label' => function (?Pessoa $pessoa) {
                    return $pessoa ? $pessoa->getNome() : '';
                },
                'required' => false
            ));

            $builder->add('descricao', TextType::class, array(
                'label' => 'Descrição',
                'required' => false
            ));

            $builder->add('documentoBanco', EntityType::class, array(
                'label' => 'Banco do Documento',
                'class' => Banco::class,
                'choices' => $this->doctrine->getRepository(Banco::class)
                    ->findAll(WhereBuilder::buildOrderBy('codigoBanco')),
                'choice_label' => function (Banco $banco) {
                    return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
                },
                'required' => false
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
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false
            ));

            $builder->add('descontos', MoneyType::class, array(
                'label' => 'Descontos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false
            ));

            $builder->add('acrescimos', MoneyType::class, array(
                'label' => 'Acréscimos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false
            ));

            $builder->add('valorTotal', MoneyType::class, array(
                'label' => 'Valor Total',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'disabled' => true,
                'required' => false
            ));

            // Cheque

            $builder->add('chequeBanco', EntityType::class, array(
                'label' => 'Banco',
                'class' => Banco::class,
                'choices' => $this->doctrine->getRepository(Banco::class)
                    ->findAll(WhereBuilder::buildOrderBy('codigoBanco')),
                'choice_label' => function (Banco $banco) {
                    return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
                },
                'required' => false
            ));

            $builder->add('chequeAgencia', TextType::class, array(
                'label' => 'Agência',
                'required' => false
            ));

            $builder->add('chequeConta', TextType::class, array(
                'label' => 'Conta',
                'required' => false
            ));

            $builder->add('chequeNumCheque', TextType::class, array(
                'label' => 'Núm Cheque',
                'required' => false
            ));

            // Recorrência

            $builder->add('recorrente', ChoiceType::class, array(
                'label' => 'Recorrente',
                'choices' => array(
                    'SIM' => true,
                    'NÃO' => false
                ),
                'required' => false
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
}