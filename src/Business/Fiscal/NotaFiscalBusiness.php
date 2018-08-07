<?php
namespace App\Business\Fiscal;

use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use App\Entity\Estoque\Fornecedor;
use App\Entity\Fiscal\NotaFiscal;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotaFiscalBusiness
{

    private $doctrine;

    private $unimakeBusiness;

    public function __construct(RegistryInterface $doctrine, UnimakeBusiness $unimakeBusiness)
    {
        $this->doctrine = $doctrine;
        $this->unimakeBusiness = $unimakeBusiness;
    }

    public function handleNotaSaida(NotaFiscal $nf)
    {
        if ($nf->getId()) {
            throw new \Exception("Nota Fiscal já tratada.");
        }
        
        $ambiente = getenv("BONSUCESSO_FISCAL_AMBIENTE");
        if (! $ambiente) {
            throw new \Exception("Ambiente não encontrado");
        }
        
        $nf->setAmbiente($ambiente);
        
        // O Spartacus está utilizando o campo SERIE para decidir quais notas imprimir automaticamente
        // Serie 2 (Bonsucesso), Serie 3 (Ipê)
        $chave = "BONSUCESSO_FISCAL_" + strtoupper($nf->getTipoNotaFiscal()) + "_SERIE";
        $serie = getenv($chave);
        if (! $serie) {
            throw new \Exception('Erro ao pesquisar chave [' . $chave . ']');
        }
        $nf->setSerie($serie);
        
        $nnf = $this->doctrine->getRepository(NotaFiscal::class)->findProxNumFiscal($ambiente == 'PROD', $nf->getSerie(), $nf->getTipoNotaFiscal());
        
        $nf->setNumero($nnf);
        
        $emitente = $this->doctrine->getRepository(Pessoa::class)->findByDocumento('77498442000134');
        $nf->setPessoaEmitente($emitente);
    }

    /**
     * Calcula o total da nota e o total de descontos.
     *
     * @param
     *            nf
     */
    public function calcularTotais(NotaFiscal $nf)
    {
        $bdTotal = 0.0;
        $bdSubTotal = 0.0;
        $bdDescontos = 0.0;
        foreach ($nf->getItens() as $item) {
            $bdTotal += $item->getValorTotal();
            $bdSubTotal += $item->getSubTotal();
            $bdDescontos += $item->getValorDesconto() ? $item->getValorDesconto() : 0.0;
        }
        
        $nf->setSubTotal($bdSubTotal);
        $nf->setValorTotal($bdTotal);
        $nf->setTotalDescontos($bdDescontos);
    }

    public function corrigirPessoaDestinatario(NotaFiscal $nf)
    {
        $documento = $nf->getPessoaDestinatario()->getDocumento();
        
        if ($nf->getPessoaCadastro() == null) {
            
            $cliente = $this->doctrine->getRepository(Cliente::class)->findByDocumento($documento);
            
            if ($cliente) {
                $nf->setPessoaCadastro('CLIENTE');
            } else {
                $fornecedor = $this->doctrine->getRepository(Fornecedor::class)->findByDocumento($documento);
                if ($fornecedor) {
                    $nf->setPessoaCadastro('FORNECEDOR');
                } else {
                    throw new \Exception("Destinatário não encontrado em Clientes ou Fornecedores.");
                }
            }
        } else {
            if ('CLIENTE' == $nf->getPessoaCadastro()) {
                $cliente = $this->doctrine->getRepository(Cliente::class)->findByDocumento($documento);
                if (! $cliente) {
                    $fornecedor = $this->doctrine->getRepository(Fornecedor::class)->findByDocumento($documento);
                    if (! $fornecedor) {
                        throw new \Exception("Destinatário não encontrado em Clientes ou Fornecedores.");
                    }
                }
            } else {
                $fornecedor = $this->doctrine->getRepository(Fornecedor::class)->findByDocumento($documento);
                if (! $fornecedor) {
                    $cliente = $this->doctrine->getRepository(Cliente::class)->findByDocumento($documento);
                    if (! $cliente) {
                        throw new \Exception("Destinatário não encontrado em Clientes ou Fornecedores.");
                    }
                }
            }
        }
        
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($nf);
        $entityManager->flush();
        
        return $nf;
    }

    public function faturar(NotaFiscal $notaFiscal)
    {
        $this->unimakeBusiness->genNFeXML($notaFiscal);
    }

    public function consultarStatus(NotaFiscal $notaFiscal)
    {
        return $this->unimakeBusiness->consultarStatus($notaFiscal);
    }

    /**
     * Só exibe o botão faturar se tiver nestas condições.
     * Lembrando que o botão "Faturar" serve tanto para faturar a primeira vez, como para tentar faturar novamente nos casos de erros.
     *
     * @param
     *            venda
     * @return
     */
    public function permiteFaturamento(NotaFiscal $notaFiscal)
    {
        // Se o status for 100, não precisa refaturar.
        if ($notaFiscal->getSpartacusStatus()) {
            // aprovada
            if ("100" == $notaFiscal->getSpartacusStatus()) {
                return false;
            }
            // cancelada
            if ("101" == $notaFiscal->getSpartacusStatus()) {
                return false;
            }
            if ("204" == $notaFiscal->getSpartacusStatus()) {
                return false;
            }
            
            if ("0" == $notaFiscal->getSpartacusStatus()) {
                
                if (strpos($notaFiscal->getSpartacusMensRetornoReceita(), "DUPLICIDADE DE NF") !== FALSE) {
                    return false;
                }
                
                if ("AGUARDANDO FATURAMENTO" == $notaFiscal->getSpartacusMensRetornoReceita()) {
                    if ($notaFiscal->getDtSpartacusStatus()) {
                        $dtStatus = $notaFiscal->getDtSpartacusStatus();
                        
                        $agora = new \DateTime();
                        $diff = $agora->diff($dtStatus);
                        
                        $minutos = $diff->i;
                        
                        // Se já passou 3 minutos, então permite refaturar.
                        if ($minutos > 2) {
                            return true;
                        }
                    }
                    return false;
                }
            }
        }
        
        return true;
    }
}