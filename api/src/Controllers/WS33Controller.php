<?php

namespace Source\Controllers;

use Crell\ApiProblem\ApiProblem;
use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;
use Source\Models\WS33Model;
use Source\Support\Siat;

class WS33Controller implements HttpResponseCodeInterface
{
    /**
     * Responsável por receber a comunicação do WS 33.
     *
     * @return void
     */
    public function index(): void
    {
        // Obter parâmetros de consulta
        $queryParams = Route::getQueryParams();

        // Verificar se não houve parâmetro de consulta informado
        if (!$queryParams) {
            // Retornar resposta
            echo (new ApiProblem('A definir'))
                ->setStatus(self::INTERNAL_SERVER_ERROR)
                ->setDetail('O que deverá acontecer se nenhum parâmetro for informado?')
                ->asJson();

            // Finalizar execução
            exit;
        }

        // Obter parâmetros de consulta
        $agreement = $queryParams['nu_convenio'] ?? null;
        $guide = $queryParams['nu_nosso_numero'] ?? null;

        // Declarar variáveis de intervalo de datas
        $initialDate = null;
        $finalDate = null;

        // Verificar se o parâmetro 'nu_nosso_numero' não foi informado
        if (!$guide) {
            // Consultar na tabela ws33
            $findWs33 = (new WS33Model())
                ->where('orgao = :orgao AND criado_em < CURDATE()', ['orgao' => $agreement])
                ->orderBy('id DESC')
                ->fetch();

            // Verificar se o registro não foi encontrado
            if (!$findWs33) {
                // Retornar resposta
                echo (new ApiProblem('A definir'))
                    ->setStatus(self::INTERNAL_SERVER_ERROR)
                    ->setDetail('O que deverá acontecer se nenhum registro for encontrado?')
                    ->asJson();

                // Finalizar execução
                exit;
            }

            // Atualizar intervalo de datas
            $initialDate = date('dmY', strtotime('-2 days', strtotime($findWs33->criado_em)));
            $finalDate = date('dmY', strtotime('-2 days', 'now'));

            // Verificar se existe parâmetro para debuggar
            if (isset($queryParams['debug'])) {
                debug("{$initialDate} - {$finalDate}", $findWs33);
            }
        }

        // Preparar XML de consulta para o SIAT
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WebServiceGTM">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <urn:consultaPagamento>
                            <!--Optional:-->
                            <mensagem>
                                <![CDATA[
                                <Entrada>
                                    <EntradaConsultaPagamento>
                                        <periodoInicial>' . $initialDate . '</periodoInicial>
                                        <periodoFinal>' . $finalDate . '</periodoFinal>
                                        <codigoGuia>' . $guide . '</codigoGuia>
                                        <sistemaLancamentoEspecializado>' . $agreement . '</sistemaLancamentoEspecializado>
                                        <tipoProcedencia/>
                                        <chaveSistemaLancamentoEspecializado/>
                                    </EntradaConsultaPagamento>
                                </Entrada>
                                ]]>
                            </mensagem>
                        </urn:consultaPagamento>
                    </soapenv:Body>
                    </soapenv:Envelope>';

        debug('oi');
        // Realizar consulta ao SIAT
        (new Siat())->call($xml);
    }
}