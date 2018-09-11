<?php

namespace App\Business\Financeiro;

use App\Entity\Base\DiaUtil;
use App\Entity\Base\Endereco;
use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use App\Entity\Financeiro\BandeiraCartao;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\GrupoItem;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\OperadoraCartao;
use App\Entity\Financeiro\RegraImportacaoLinha;
use App\EntityHandler\CRM\ClienteEntityHandler;
use App\Repository\Financeiro\CategoriaRepository;
use App\Utils\DateTimeUtils;
use App\Utils\Repository\FilterData;
use App\Utils\StringUtils;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints\Date;

const TXT_LINHA_NAO_IMPORTADA = "<<< LINHAS NÃO IMPORTADAS >>>";

const TXT_LINHA_IMPORTADA = "<<< LINHAS IMPORTADAS >>>";

class MovimentacaoImporter
{

    private $doctrine;

    private $linhas;

    /**
     * Armazena linhas de descrição complementar para verificações durante o processo.
     *
     * @var array
     */
    private $linhasComplementares;

    /**
     * Armazena as movimentações de categoria 1.01 que já foram importadas.
     *
     * @var array
     */
    private $movs101JaImportadas = array();

    /**
     * Para armazenar as movimentações já importadas afim de que não sejam importadas 2x por duplicidade.
     *
     * @var array
     */
    private $movsJaImportadas = array();


    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param $tipoExtrato
     * @param $linhasExtrato
     * @param $carteiraExtrato
     * @param $carteiraDestino
     * @param $grupoItem
     * @param $gerar
     * @return mixed
     * @throws \Exception
     */
    public function importar($tipoExtrato, $linhasExtrato, $carteiraExtrato, $carteiraDestino, $grupoItem, $gerar)
    {
        switch ($tipoExtrato) {
            case 'EXTRATO_GRUPO_MOVIMENTACOES':
                return $this->importGrupoMovimentacao($grupoItem);
            default:
                return $this->importarPadrao($linhasExtrato, $tipoExtrato, $carteiraExtrato, $carteiraDestino, $gerar);
        }
    }

    /**
     * @param $linhasExtrato
     * @param $tipoExtrato
     * @param $carteiraExtrato
     * @param $carteiraDestino
     * @param $gerar
     * @return mixed
     * @throws \Exception
     */
    private function importarPadrao($linhasExtrato, $tipoExtrato, $carteiraExtrato, $carteiraDestino, $gerar)
    {

        $movsImportadas = array();

        $linhasNaoImportadas = array();
        $linhasImportadas = array();

        $this->linhas = explode("\n", $linhasExtrato);

        foreach ($this->linhas as $linha) {
            if (!$linha or $linha === TXT_LINHA_NAO_IMPORTADA or $linha === TXT_LINHA_IMPORTADA) {
                continue;
            }

            if (in_array($linha, $this->linhasComplementares)) {
                $linhasImportadas[] = $linha;
                continue;
            }

            $movimentacao = $this->importarLinha($linha, $tipoExtrato, $carteiraExtrato, $carteiraDestino, $gerar);

            if ($movimentacao) {
                $movsImportadas[] = $movimentacao;
            } else {
                $linhasNaoImportadas[] = $linha;
            }
        }

        $r['LINHAS_RESULT'] = TXT_LINHA_NAO_IMPORTADA . "\n" .
            implode("\n", $linhasNaoImportadas) . "\n" .
            TXT_LINHA_IMPORTADA . "\n\n" .
            implode("\n", $linhasImportadas);
        $r['movs'] = $movsImportadas;

        return $r;
        return $r;
    }

    /**
     * @param $linha
     * @param $tipoExtrato
     * @param $carteiraExtrato
     * @param $carteiraDestino
     * @param $gerar
     * @return mixed
     * @throws \Exception
     */
    private function importarLinha($linha, $tipoExtrato, $carteiraExtrato, $carteiraDestino, $gerar)
    {
        switch ($tipoExtrato) {
            case "EXTRATO_SIMPLES":
                $camposLinha = $this->importLinhaExtratoSimples($linha);
                break;
            case "EXTRATO_MODERNINHA_DEBITO":
                $camposLinha = $this->importLinhaExtratoModerninhaDebito($linha);
                break;
            case "EXTRATO_CIELO_DEBITO":
                $camposLinha = $this->importLinhaExtratoCieloDebito($linha);
                break;
            case "EXTRATO_CIELO_CREDITO":
                $camposLinha = $this->importLinhaExtratoCieloCredito($linha);
                break;
            case "EXTRATO_STONE_DEBITO":
                $camposLinha = $this->importLinhaExtratoStoneDebito($linha);
                break;
            case "EXTRATO_STONE_CREDITO":
                $camposLinha = $this->importLinhaExtratoStoneCredito($linha);
                break;
            default:
                throw new \Exception("Tipo de extrato inválido.");
        }

        if (strpos($tipoExtrato, "DEBITO") !== FALSE) {
            return $this->handleLinhaImportadaDebito($tipoExtrato, $carteiraExtrato, $carteiraDestino, $camposLinha);
        } else {
            return $this->handleLinhaImportadaPadrao($linha, $tipoExtrato, $carteiraExtrato, $carteiraDestino, $camposLinha);
        }
    }

    /**
     * @param $tipoExtrato
     * @param $carteiraExtrato
     * @param $carteiraDestino
     * @param $camposLinha
     * @return \App\Entity\Financeiro\Carteira|Movimentacao|OperadoraCartao
     * @throws \Exception
     */
    private function handleLinhaImportadaDebito($tipoExtrato, $carteiraExtrato, $carteiraDestino, $camposLinha)
    {
        $descricao = $camposLinha["descricao"];
        $dtMoviment = $camposLinha["dtMoviment"];
        $dtVenctoEfetiva = $camposLinha["dtVenctoEfetiva"];
        $valor = $camposLinha["valor"];
        $desconto = $camposLinha["desconto"];
        $valorTotal = $camposLinha["valorTotal"];
        $bandeiraCartao = $camposLinha["bandeiraCartao"];


        $categ101 = $this->doctrine->getRepository(CategoriaRepository::class)->find(101);
        $categ299 = $this->doctrine->getRepository(CategoriaRepository::class)->find(299);


        $modo = "RECEB. CARTÃO DÉBITO";

        // carteiraDestino aqui tem que ser sempre o Caixa a Vista (que é o caixa que primeiro recebeu o valor da compra).


        // Primeiro tento encontrar a movimentação original do cartão, que é a movimentação de entrada (101) no caixa a vista (anotado na folhinha de fechamento de caixa, lançado manualmente).

        $movs101Todas = $this->doctrine->getRepository(Movimentacao::class)
            ->findBy([
                'dtMoviment' => $dtMoviment,
                'valorTotal' => $valorTotal,
                'carteira' => $carteiraDestino,
                'bandeiraCartao' => $bandeiraCartao,
                'categoria' => $categ101
            ]);

        $movs101 = array();
        foreach ($movs101Todas as $mov101) {
            if (!in_array($mov101->getId(), $this->movs101JaImportadas)) {
                $movs101[] = $mov101;
            }
        }

        // FIXME: na lógica do Java, se não encontrava ele ia procurar no próximo dia.

        if (count($movs101) < 1) {
            throw new \Exception('Movimentação (1.01) original não encontrada (' . $descricao . ' - R$ ' . number_format($valorTotal, 2, '.', ','));
        }

        // Por padrão, será a primeira. Mas se encontrar outra sem cadeia, troca.
        $mov101 = $movs101[0];
        foreach ($movs101 as $mov101_) {
            if (!$mov101_->getCadeia()) {
                $mov101 = $mov101_;
                break;
            }
        }

        // Aqui as carteiras são invertidas, pois é a 299 (a destino do método é a do extrato, e a destino da importação é a 'origem' no método)
        $movs299Todas = $this->doctrine->getRepository(Movimentacao::class)
            ->findBy([
                'dtMoviment' => $dtMoviment,
                'valorTotal' => $valorTotal,
                'carteira' => $carteiraExtrato,
                'carteiraDestino' => $carteiraDestino,
                'bandeiraCartao' => $bandeiraCartao,
                'categoria' => $categ299
            ]);
        $movs299 = array();
        // Remove as já importadas para resolver o bug de ter duas movimentações de mesma bandeira e mesmo valor no mesmo dia
        foreach ($movs299Todas as $mov299) {
            if (!in_array($mov299->getId(), $this->movsJaImportadas)) {
                $movs299[] = $mov299;
            }
        }

        // Se já existir, só retorna pois já encontrou a que já tinha sido $importada->..
        if (count($movs299) < 1) {
            return $movs299[0];
        }

        if ($mov101->getCadeia() and
            $mov101->getCadeia()->getMovimentacoes() and
            $mov101->getCadeia()->getMovimentacoes()->count() > 0) {
            throw new \Exception("Inconsistência: movimentação original já possui uma cadeia e a movimentação que se está tentando importar não faz parte dela.");
        }

        // Crio as movimentações 299 (no caixa AV) e 199 (na carteira extrato)

        $mov299 = new Movimentacao();
        // aqui se inverte as carteiras, pois para salvar uma transferência entre carteiras se deve sempre começar pela 299 (ver como funciona o MovimentacaoDataMapperImpl.processSave)
        $mov299->setCarteira($carteiraDestino); // vai debitar no 'CAIXA A VISTA'
        $mov299->setCarteiraDestino($carteiraExtrato); // vai creditar na carteira do cartão (199)
        $mov299->setCategoria($categ299);
        $mov299->setValor($valor);
        $mov299->setDescontos($desconto);
        $mov299->setValorTotal($valorTotal);
        $mov299->setDescricao($descricao);
        $mov299->setTipoLancto('TRANSF_PROPRIA'); // para gerar as duas (299+199)
        $mov299->setStatus('REALIZADA');
        $mov299->setModo($modo);
        $mov299->setDtMoviment($mov101->getDtMoviment());
        $mov299->setDtVencto($dtVenctoEfetiva);
        $mov299->setDtVenctoEfetiva($dtVenctoEfetiva); // por questão de informação, a data efetiva em que o cartão pagou o valor fica na dt vencto nossa
        // tenho que deixar a dtPagto como a dtMoviment porque a 299
        // no caixa a vista tem que ser com a mesma data da 101 (que foi lançada através do fechamento de caixa diário).
        // e não posso ter uma 199 com data diferente da 299 correspondente
        $mov299->setDtPagto($mov101->getDtMoviment());

        $mov299->setBandeiraCartao($bandeiraCartao);


        $operadoraCartao = $this->doctrine->getRepository(OperadoraCartao::class)->findBy(['carteira' => $carteiraExtrato]);

        $mov299->setOperadoraCartao($operadoraCartao);

        return $mov299;
    }

    private function handleLinhaImportadaPadrao($linha, $tipoExtrato, $carteiraExtrato, $carteiraDestino, $gerarSemRegras, $camposLinha)
    {
        $descricao = $camposLinha["descricao"];
        $dtMoviment = $camposLinha["dtMoviment"];
        $dtVenctoEfetiva = $camposLinha["dtVenctoEfetiva"];
        $valor = $camposLinha["valor"];
        $desconto = $camposLinha["desconto"];
        $valorTotal = $camposLinha["valorTotal"];
        $entradaOuSaida = $camposLinha["entradaOuSaida"];
        $modo = $camposLinha["modo"];
        $categoriaCodigo = $camposLinha["categoriaCodigo"];
        $planoPagtoCartao = $camposLinha["planoPagtoCartao"];
        $bandeiraCartao = $camposLinha["bandeiraCartao"];
        $numCheque = null;

        $valorNegativo = $valor < 0.0;
        $valor = abs($valor);

        $regras = $this->doctrine->getRepository(RegraImportacaoLinha::class)->findAllBy($carteiraExtrato);
        $regra = null;
        foreach ($regras as $r) {
            if ($r->getRegraRegexJava()) {
                if (preg_match($r->getRegraRegexJava(), $descricao)) {
                    if ($r->getSinalValor() === 0 or
                        ($r->getSinalValor() === -1 and $valorNegativo) or
                        ($r->getSinalValor() === 1 and !$valorNegativo)) {
                        $regra = $r;
                        break;
                    }
                }
            }
        }

        if ($regra) {
//            numCheque = StringUtils.getGroupFromRegex(linha, $regra->getRegraRegexJava(), "NUMCHEQUE");
//            if (numCheque != null) {
//                numCheque = numCheque.replaceAll("[^\\d]", "");
//                // convertendo para integer para remover zeros a esquerda
//                Integer numChequeInt = Integer.parseInt(numCheque);
//				numCheque = numChequeInt.toString();
//			}
        }

        if ($numCheque and valorNegativo) {
            $modo = $this->doctrine->getRepository(Modo::class)->find(3); // CHEQUE PRÓPRIO
        } else if (numCheque != null && !valorNegativo) {
            $modo = $this->doctrine->getRepository(Modo::class)->find(4); // CHEQUE TERCEIROS
        }

        // Primeiro tenta encontrar movimentações em aberto de qualquer carteira, com o mesmo valor e dtVencto
        // Depois tenta encontrar movimentações de qualquer status somente da carteira do extrato
        // Junto os dois resultados
        $fdMovsAbertas = array();
        $fd_dtVenctoEfetiva = new FilterData('dtVenctoEfetiva', 'EQ', $dtVenctoEfetiva);
        $fd_valor = new FilterData('valor', 'EQ', $valor);
        $fd_categ_codigoSuper = new FilterData('categ.codigoSuper', 'EQ', $entradaOuSaida);
        $fd_modo = new FilterData('modo', 'EQ', $modo);
        $fd_status = new FilterData('status', 'IN', ['A_COMPENSAR', 'ABERTA']);
        $fd_carteira = new FilterData('carteira', 'EQ', $carteiraExtrato);
        $fd_cheque = new FilterData('numCheque', 'LIKE_ONLY', '%' . $numCheque);

        $movsAbertas = $this->doctrine->getRepository(Movimentacao::class)
            ->findByFilters([
                $fd_dtVenctoEfetiva,
                $fd_valor,
                $fd_categ_codigoSuper,
                $fd_modo,
                $fd_status,
                $fd_cheque
            ], null, 0, 0);

        $movsTodas = $this->doctrine->getRepository(Movimentacao::class)
            ->findByFilters([
                $fd_dtVenctoEfetiva,
                $fd_valor,
                $fd_categ_codigoSuper,
                $fd_modo,
                $fd_carteira,
                $fd_cheque
            ], null, 0, 0);

        // array para atribuir a união dos outros dois
        $movs = array();
        foreach ($movsAbertas as $mov) {
            if (!in_array($mov->getId(), $movs) and (!in_array($mov->getId(), $this->movsJaImportadas))) {
                $movs[] = $mov->getId();
            }
        }
        foreach ($movsTodas as $mov) {
            if (!in_array($mov->getId(), $movs) and (!in_array($mov->getId(), $this->movsJaImportadas))) {
                $movs[] = $mov->getId();
            }
        }


        // Se achou alguma movimentação já lançada, pega a primeira
        if (count($movs) > 0) {
            $movimentacao = $this->doctrine->getRepository(Movimentacao::class)->find($movs[0]);
            $movimentacao->setDtPagto($dtVenctoEfetiva);
            $movimentacao->setStatus('REALIZADA');
            $movimentacao->setCarteira($carteiraExtrato);
            return $movimentacao;
        } else {
            if ($regra) {
                $movimentacao = new Movimentacao();

//                $movimentacao->setUnqControle(com.ocabit.utils.strings.StringUtils.generateRandomString(15));

                $carteiraOrigem = $regra->getCarteira() ? $regra->getCarteira() : $carteiraExtrato;
                $carteiraDestino = $regra->getCarteiraDestino() ? $regra->getCarteiraDestino() : $carteiraDestino;

                if ($regra->getTipoLancto() == 'TRANSF_PROPRIA') {
                    // Nas transferências entre contas próprias, a regra informa a carteira de origem.
                    // A de destino, se não for informada na regra, será a do extrato.

                    if (!$regra->getCategoria()->getCodigo() == "299") {
                        throw new \Exception("Regras para transferências entre carteiras próprias devem ser apenas com categoria 2.99");
                    }

                    // Se a regra informar a carteira da 299, prevalesce
                    $cart299 = $regra->getCarteira() ? $regra->getCarteira() : $carteiraExtrato;

                    $cart199 = $regra->getCarteiraDestino();
                    if ((!$cart199) or $cart199->getCodigo() == '99') {
                        $cart199 = $carteiraExtrato;
                    }

                    $movimentacao->setCarteira($cart299);
                    $carteiraDestino = $cart199;
                    $movimentacao->setCarteiraDestino($carteiraDestino);
                    // se NÃO for regra para TRANSF_PROPRIA
                } else {
                    if ($regra->getTipoLancto() == 'CHEQUE_PROPRIO') {

                        $movimentacao = $this->doctrine->getRepository(Movimentacao::class)
                            ->findBy([
                                'valor' => $valorTotal,
                                'carteira' => $carteiraExtrato,
                                'numCheque' => $numCheque
                            ]);


                        if (in_array($movimentacao->getId(), $this->movsJaImportadas)) {
                            $movimentacao = null;
                        }

                        // Se achou a movimentação deste cheque, só seta a dtPagto
                        if (!$movimentacao) {
                            $movimentacao->setDtPagto($dtVenctoEfetiva);
                            return $movimentacao;
                        } else {
                            $movimentacao = new Movimentacao();
//                            $movimentacao->setUnqControle(com.ocabit.utils.strings.StringUtils.generateRandomString(15));
                            $movimentacao->setNumCheque($numCheque);
                            $movimentacao->setBanco($regra->getCarteira()->getBanco());
                            $movimentacao->setAgencia($regra->getCarteira()->getAgencia());
                            $movimentacao->setConta($regra->getCarteira()->getConta());
                        }

                    } else if ($regra->getTipoLancto() == 'CHEQUE_TERCEIROS') {
                        $movimentacao->setChequeNumCheque($numCheque);

                        if ($regra->getCheque()) {
                            $movimentacao->setAgencia($regra->getCheque()->getAgencia());
                            $movimentacao->setConta($regra->getCheque()->getConta());
                            $movimentacao->setBanco($regra->getCheque()->getBanco());
                        } else {
                            $movimentacao->setAgencia('9999');
                            $movimentacao->setConta('99999-9');
                            $movimentacao->setBanco(null);
                        }
                    }
                }

                $movimentacao->setTipoLancto($regra->getTipoLancto());

                $movimentacao->setCarteira($carteiraOrigem);
                if ($movimentacao->getTipoLancto() == 'TRANSF_PROPRIA') {
                    $movimentacao->setCarteiraDestino($carteiraDestino);
                }

                $movimentacao->setDescricao($descricao);

                $movimentacao->setCategoria($regra->getCategoria());
                $movimentacao->setCentroCusto($regra->getCentroCusto());

                $movimentacao->setDtMoviment($dtVenctoEfetiva);
                $movimentacao->setDtVencto($dtVenctoEfetiva);

                $movimentacao->setStatus($regra->getStatus());

                $movimentacao->setModo($regra->getModo());
                $movimentacao->setValor($valor);
                $movimentacao->setValorTotal($valor);

                if ($regra->getStatus() . equals('REALIZADA')) {
                    $movimentacao->setDtPagto($dtVenctoEfetiva);
                }

                $movimentacao->setPlanoPagtoCartao($planoPagtoCartao);

                $movimentacao->setNumCheque($numCheque);

                return $movimentacao;
            } else if ($gerarSemRegras) {


                // se for pra gerar movimentações que não se encaixem nas regras...
                $movimentacao = new Movimentacao();
                // $movimentacao->setUnqControle(com.ocabit.utils.strings.StringUtils.generateRandomString(15));
                $movimentacao->setCarteira($carteiraExtrato);
                $movimentacao->setValor($valor);
                $movimentacao->setDescontos($desconto);
                $movimentacao->setValorTotal($valorTotal);
                $movimentacao->setDescricao($descricao);
                $movimentacao->setTipoLancto('REALIZADA');
                $movimentacao->setStatus('REALIZADA');
                $movimentacao->setModo($modo);
                $movimentacao->setDtMoviment($dtMoviment);
                $movimentacao->setDtVencto($dtVenctoEfetiva);
                $movimentacao->setDtVenctoEfetiva($dtVenctoEfetiva);
                $movimentacao->setDtPagto($dtVenctoEfetiva);
                $movimentacao->setBandeiraCartao($bandeiraCartao);
                $movimentacao->setPlanoPagtoCartao($planoPagtoCartao);

                if ($categoriaCodigo) {
                    $categoria = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => $categoriaCodigo]);
                } else {
                    if (valorNegativo) {
                        $categoria = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 2]);
                    } else {
                        $categoria = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 1]);
                    }
                }
                $movimentacao->setCategoria($categoria);

                return movimentacao;
            }
        }


    }


    /**
     * Verifica se é uma linha normal (DATA DESCRIÇÃO VALOR) ou não.
     * @param $linha
     * @return bool
     */
    private function ehLinhaExtratoSimplesOuSaldo($linha)
    {
        if (strpos(str_replace(" ", "", $linha) . replace(" ", ""), 'SALDO' === FALSE)) {
            return true;
        }
        if (preg_match(StringUtils::PATTERN_DATA, $linha, $matches)) {
            return true;
        }

        if (preg_match(StringUtils::PATTERN_MONEY, $linha, $matches)) {
            return true;
        }
    }


    private function importLinhaExtratoSimples($numLinha)
    {
        $linha = $this->linhas[$numLinha];
        preg_match(StringUtils::PATTERN_DATA, $linha, $matches);
        $dataStr = $matches['data'];

        preg_match(StringUtils::PATTERN_MONEY, $linha, $matches);
        $valorStr = $matches['money'];

        $dataIncompleta = false;
        $dataPequena = true;

        $ano = date('Y');
        if (strlen($dataStr) == 5) {
            $dataStr .= "/" . $ano;
            $dataIncompleta = true;
        } else if (strlen($dataStr) == 8) {
            $dataPequena = true;
        } else if (strlen($dataStr) != 10) {
            throw new \Exception("Erro ao ler a data na linha: [" . $linha . "]");
        }

        $dateFormat = $dataPequena ? "d/m/y" : "d/m/Y";

        $dtVenctoEfetiva = \DateTime::createFromFormat($dateFormat, $dataStr);

        if ($dataIncompleta and DateTimeUtils::monthDiff(new \DateTime('now'), $dtVenctoEfetiva) > 2) {
            $dtVenctoEfetiva->setDate($dtVenctoEfetiva->format('Y'), $dtVenctoEfetiva->format('m') - 12, $dtVenctoEfetiva->format('d'));
        }

        $valorComSufixo = preg_replace("@[^0-9,-.D]@", "", substr($linha, strpos($linha, $valorStr)));
        $sufixo = $valorComSufixo[strlen($valorComSufixo) - 1];

        if (($sufixo == 'D') or ($sufixo == '-')) {
            $valorStr = "-" . $valorStr;
        }

        $valorStr = preg_replace("@[^0-9\\.\\,-]@", "", $valorStr);
        $valor = StringUtils::parseFloat($valorStr);

        $entradaOuSaida = $valor < 0 ? 2 : 1;

        $descricao = str_replace($valorStr, "", str_replace($dataStr, "", $linha));

        // Se ainda não for a última linha...
        if ($numLinha < count($this->linhas) - 1) {
            // ...verifica se a próxima linha é uma linha completa (DATA DESCRIÇÃO VALOR), ou se é uma linha de complemento da linha anterior
            $linhaComplementar = $this->linhas[$numLinha + 1];
            if (!$this->ehLinhaExtratoSimplesOuSaldo($linhaComplementar)) {
                $linhasComplementares[] = $numLinha + 1;
                $descricao .= " (" . trim($linhaComplementar) . ")";
            }
        }
        $descricao = str_replace("  ", " ", $descricao);

        $camposLinha["descricao"] = $descricao;
        $camposLinha["dtVenctoEfetiva"] = $dtVenctoEfetiva;
        $camposLinha["dtMoviment"] = $dtVenctoEfetiva; // passo o mesmo por se tratar de extrato simples (diferente de extrato de cartão).
        $camposLinha["valor"] = $valor;
        $camposLinha["desconto"] = null;
        $camposLinha["valorTotal"] = $valor;
        $camposLinha["entradaOuSaida"] = $entradaOuSaida;
        $camposLinha["modo"] = null;

        return $camposLinha;
    }

    private function importGrupoMovimentacao(GrupoItem $grupoItem)
    {
        $movimentacoes = array();

        for ($i = 0; $i < count($this->linhas); $i++) {
            $linha = $this->linhas[$i];
            if (!$linha or $linha === TXT_LINHA_NAO_IMPORTADA or $linha === TXT_LINHA_IMPORTADA) {
                continue;
            }

            $camposLinha = $this->importLinhaExtratoSimples($i);

            $descricao = $camposLinha["descricao"];
            $dtMoviment = $camposLinha["dtMoviment"];
            $dtVenctoEfetiva = $camposLinha["dtVenctoEfetiva"];
            $valor = $camposLinha["valor"];
            $desconto = $camposLinha["desconto"];
            $valorTotal = $camposLinha["valorTotal"];

            // Tenta encontrar uma movimentação com as características passadas.
            $movs = $this->doctrine->getRepository(Movimentacao::class)
                ->findBy([
                    'dtMoviment' => $dtMoviment,
                    'valor' => $valor,
                    'grupoItem' => $grupoItem
                ]);

            $importada = null;
            if ($movs and count($movs) > 0) {
                $importada = $movs[0];
            }

            if ($importada and !$importada->getDtPagto()) {
                $importada->setStatus('REALIZADA');
                $importada->setDtPagto($dtVenctoEfetiva);
            } else {

                $importada = new Movimentacao();
//                $importada->setUnqControle(com.ocabit.utils.strings.StringUtils.generateRandomString(15));

                $importada->setGrupoItem($grupoItem);

                $categ101 = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => '202001']);  // 2.02.001 - CUSTOS DE MERCADORIAS
                $importada->setCategoria($categ101);


                $importada->setCentroCusto($this->doctrine->getRepository(CentroCusto::class)->find(1));

                $importada->setModo($this->doctrine->getRepository(Modo::class)->find(50));

                $importada->setValor($valor);
                $importada->setDescontos($desconto);
                $importada->setValorTotal($valorTotal);

                $importada->setDescricao(str_replace("  ", " ", $descricao));

                $importada->setTipoLancto('MOVIMENTACAO_AGRUPADA');
                $importada->setStatus('REALIZADA');

                $importada->setDtMoviment($dtMoviment);
                $importada->setDtVencto($dtVenctoEfetiva);
                $importada->setDtVenctoEfetiva($dtVenctoEfetiva);
                $importada->setDtPagto($dtVenctoEfetiva);

                $importada->setBandeiraCartao(null);
            }

            $movimentacoes[] = $importada;

        }

        return $movimentacoes;
    }

    private function importLinhaExtratoModerninhaDebito($numLinha)
    {
        /**
         * 0 - Data_Transacao
         * 1 - "MODERNINHA"
         * 2 - Tipo_Pagamento
         * 3 - Transacao_ID
         * 4 - Valor_Bruto
         */
        $linha = $this->linhas[$numLinha];
        $camposLinha = array();
        $campos = explode("\t", $linha);

        if (count($campos) < 4) {
            return null;
        }

        $dtVenda = DateTimeUtils::parseDateStr($campos[0]);
        $valor = abs(StringUtils::parseFloat($campos[4], true));
        $entradaOuSaida = $valor < 0 ? 2 : 1;

        $descricao = $campos[1] . " - " . $campos[3] . " (" . $campos[2] . ")";
        $descricao = preg_replace("@\n|\r|\t@", "", $descricao);

        $bandeira = "N INF DÉB";

        $modo = $this->doctrine->getRepository(Modo::class)->find(10); // "RECEB. CARTÃO DÉBITO"

        $bandeiraCartao = $this->doctrine->getRepository(BandeiraCartao::class)->findByLabelsAndModo($bandeira, $modo);
        if (!$bandeiraCartao) {
            throw new \Exception("Bandeira Cartão não encontrada");
        }

        $camposLinha["bandeiraCartao"] = $bandeiraCartao;
        $camposLinha["planoPagtoCartao"] = 'DEBITO';
        $camposLinha["descricao"] = $descricao;
        $camposLinha["dtMoviment"] = $dtVenda;
        $camposLinha["dtVenctoEfetiva"] = $dtVenda;
        $camposLinha["valor"] = $valor;
        $camposLinha["valorTotal"] = $valor;
        $camposLinha["entradaOuSaida"] = $entradaOuSaida;
        $camposLinha["categoriaCodigo"] = 199;

        return $camposLinha;
    }


    private function importLinhaExtratoCieloDebitoNovo($numLinha)
    {
        $linha = $this->linhas[$numLinha];
        $campos = explode("\t", $linha);
        if (count($campos) < 10) {
            return null;
        }

        /**
         * 0 Data da venda
         * 1 Data da autorização
         * 2 Bandeira
         * 3 Forma de pagamento
         * 4 Quantidade de parcelas
         * 5 Valor da venda
         * 6 Taxa de administração (%)
         * 7 Valor descontado
         * 8 Previsão de pagamento
         * 9 Valor líquido da venda
         * 10 Número Lógico
         */

        $dtVenda = DateTimeUtils::parseDateStr($campos[0]);
        $valor = abs(StringUtils::parseFloat($campos[5], true));
        $entradaOuSaida = $valor < 0 ? 2 : 1;
        $descricao = "DÉBITO " . $campos[2]; // + " " + campos[1] + " (" + campos[4] + ")";
        $modo = $this->doctrine->getRepository(Modo::class)->find(10); // "RECEB. CARTÃO DÉBITO"

        $bandeiraCartao = $this->doctrine->getRepository(BandeiraCartao::class)->findByLabelsAndModo($campos[2], $modo);
        if (!$bandeiraCartao) {
            throw new \Exception("Bandeira Cartão não encontrada");
        }

        $camposLinha["bandeiraCartao"] = $bandeiraCartao;
        $camposLinha["planoPagtoCartao"] = 'DEBITO';
        $camposLinha["descricao"] = $descricao;
        $camposLinha["dtMoviment"] = $dtVenda;
        $camposLinha["dtVenctoEfetiva"] = $dtVenda;
        $camposLinha["valor"] = $valor;
        $camposLinha["valorTotal"] = $valor;
        $camposLinha["entradaOuSaida"] = $entradaOuSaida;

        return $camposLinha;
    }


    private function importLinhaExtratoCieloCreditoNovo($numLinha)
    {
        $linha = $this->linhas[$numLinha];
        $campos = explode("\t", $linha);
        if (count($campos) < 9) {
            return null;
        }
        /**
         * 0 Data de pagamento
         * 1 Data de venda
         * 2 Forma de pagamento
         * 3 NSU
         * 4 Número do cartão
         * 5 Valor bruto
         * 6 Status
         * 7 Valor líquido
         * 8 TID
         * 9 Taxa
         * 10 Número do EC
         * 11 Bandeira
         */

        $dtVenda = DateTimeUtils::parseDateStr($campos[1]);
        $dtPrevistaPagto = DateTimeUtils::parseDateStr($campos[0]);
        $numeroCartao = trim($campos[4]);
        $tid = trim($campos[8]);
        $codigoAutorizacao = trim($campos[10]);
        $bandeira = trim($campos[11]);
        $formaDePagamento = trim($campos[2]);
        $valor = abs(StringUtils::parseFloat($campos[5], true));

        $entradaOuSaida = $valor < 0 ? 2 : 1;


        $modo = $this->doctrine->getRepository(Modo::class)->find(9); // "RECEB. CARTÃO CRÉDITO"

        $bandeiraCartao = $this->doctrine->getRepository(BandeiraCartao::class)->findByLabelsAndModo($bandeira, $modo);
        if (!$bandeiraCartao) {
            throw new \Exception("Bandeira Cartão não encontrada");
        }

        $planoPagtoCartao = (stripos($formaDePagamento, "parc") === FALSE) ? 'CREDITO_30DD' : 'CREDITO_PARCELADO';

        $descricao = $formaDePagamento . " - " . $bandeira . " - " . $numeroCartao . " (" . $codigoAutorizacao . ") " . $tid;
        $descricao = str_replace("  ", " ", $descricao);

        $camposLinha['modo'] = $modo;
        $camposLinha['bandeiraCartao'] = $bandeiraCartao;
        $camposLinha['planoPagtoCartao'] = $planoPagtoCartao;
        $camposLinha["descricao"] = $descricao;
        $camposLinha["dtMoviment"] = $dtVenda;
        $camposLinha["dtVenctoEfetiva"] = $dtPrevistaPagto;
        $camposLinha["valor"] = $valor;
        $camposLinha["valorTotal"] = $valor;
        $camposLinha["entradaOuSaida"] = $entradaOuSaida;
        $camposLinha["categoriaCodigo"] = "101";
        return $camposLinha;
    }


    private function importLinhaExtratoStoneCredito($numLinha)
    {
        /**
         * 0 CATEGORIA
         * 1 HORA DA VENDA
         * 2 DATA DE VENCIMENTO
         * 3 TIPO
         * 4 Nº DA PARCELA
         * 5 QTD DE PARCELAS
         * 6 BANDEIRA
         * 7 STONE ID
         * 8 N° CARTÃO
         * 9 VALOR BRUTO
         * 10 VALOR LÍQUIDO
         * 11 ÚLTIMO STATUS
         * 12 DATA DO ÚLTIMO STATUS
         *
         */

        $linha = $this->linhas[$numLinha];
        $campos = explode("\t", $linha);
        if (count($campos) < 12) {
            return null;
        }

        $dtVenda = DateTimeUtils::parseDateStr($campos[1]);
        $dtPrevistaPagto = DateTimeUtils::parseDateStr($campos[2]);
        $tipo = trim($campos[3]);
        $bandeira = trim($campos[6]);

        $descricao = trim($campos[0]) + " - " + $tipo + " - " + $bandeira + " (" + trim($campos[4]) + "/" + trim($campos[5]) + ") " + trim($campos[8]);

        $valor = abs(StringUtils::parseFloat($campos[9], true));
        $entradaOuSaida = $valor < 0 ? 2 : 1;

        $modo = $this->doctrine->getRepository(Modo::class)->find(9); // "RECEB. CARTÃO CRÉDITO"

        $bandeiraCartao = $this->doctrine->getRepository(BandeiraCartao::class)->findByLabelsAndModo($bandeira, $modo);
        if (!$bandeiraCartao) {
            throw new \Exception("Bandeira Cartão não encontrada");
        }

        $planoPagtoCartao = 'DEBITO';

        $planoPagtoCartao = (stripos($descricao, "parc") === FALSE) ? 'CREDITO_30DD' : 'CREDITO_PARCELADO';

        $camposLinha['modo'] = $modo;
        $camposLinha['bandeiraCartao'] = $bandeiraCartao;
        $camposLinha['planoPagtoCartao'] = $planoPagtoCartao;
        $camposLinha["descricao"] = $descricao;
        $camposLinha["dtMoviment"] = $dtVenda;
        $camposLinha["dtVenctoEfetiva"] = $dtPrevistaPagto;
        $camposLinha["valor"] = $valor;
        $camposLinha["valorTotal"] = $valor;
        $camposLinha["entradaOuSaida"] = $entradaOuSaida;
        $camposLinha["categoriaCodigo"] = "101";
        return $camposLinha;
    }


    private function importLinhaExtratoStoneDebito($numLinha)
    {

        /**
         * 0 HORA DA VENDA
         * 1 TIPO
         * 2 BANDEIRA
         * 3 MEIO DE CAPTURA
         * 4 STONE ID
         * 5 VALOR BRUTO
         * 6 VALOR LÍQUIDO
         * 7 N° CARTÃO
         * 8 SERIAL NUMBER
         * 9 ÚLTIMO STATUS
         * 10 DATA DO ÚLTIMO STATUS
         */

        $linha = $this->linhas[$numLinha];
        $campos = explode("\t", $linha);
        if (count($campos) < 9) {
            return null;
        }

        $dtVenda = DateTimeUtils::parseDateStr($campos[0]);
        $valor = abs(StringUtils::parseFloat($campos[5], true));
        $entradaOuSaida = $valor < 0 ? 2 : 1;


        $descricao = $campos[1] . " - " . $campos[4] . " - " . $campos[7];
        $descricao = preg_replace("@\n|\r|\t@", "", $descricao);
        $modo = $this->doctrine->getRepository(Modo::class)->find(10); // "RECEB. CARTÃO DÉBITO"

        $bandeiraCartao = $this->doctrine->getRepository(BandeiraCartao::class)->findByLabelsAndModo($campos[2], $modo);
        if (!$bandeiraCartao) {
            throw new \Exception("Bandeira Cartão não encontrada");
        }

        $camposLinha["bandeiraCartao"] = $bandeiraCartao;
        $camposLinha["planoPagtoCartao"] = 'DEBITO';
        $camposLinha["descricao"] = $descricao;
        $camposLinha["dtMoviment"] = $dtVenda;
        $camposLinha["dtVenctoEfetiva"] = $dtVenda;
        $camposLinha["valor"] = $valor;
        $camposLinha["valorTotal"] = $valor;
        $camposLinha["entradaOuSaida"] = $entradaOuSaida;

        return $camposLinha;


    }

}