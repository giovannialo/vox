<?php

namespace Source\Controllers;

use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;
use Source\Models\WS33Model;
use Source\Support\Siat;
use Source\Support\Vox;

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
        if (!isset($queryParams['nu_convenio'])) {
            // Retornar erro
            Vox::apiProblem(
                'Query parameters not informed.',
                self::BAD_REQUEST,
                9003,
                'Este valor não deve ser nulo. nu_convenio'
            );
        }

        // Obter parâmetros de consulta
        $agreement = $queryParams['nu_convenio'];
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
                // Retornar erro
                Vox::apiProblem('Agreement not found.', self::NOT_FOUND, 9011, 'Registro não encontrado');
            }

            // Atualizar intervalo de datas
            $initialDate = date('dmY', strtotime('-2 days', strtotime($findWs33->criado_em)));
            $finalDate = date('dmY', strtotime('-2 days', strtotime('now')));
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

        // Realizar consulta ao SIAT
        $siat = (new Siat())->call($xml);

        // Obter resposta retornada pelo WS
        $response = $siat->Body->consultaPagamentoResponse->return->Saida->SaidaConsultaPagamento->resposta;

        // Verificar se a consulta não retornou o resultado esperado
        if ($response != 3) {
            // Verificar se foi utilizado nu_nosso_numero
            if ($guide) {
                // Retornar erro
                Vox::apiProblem('Payment not found.', self::NOT_FOUND, 9919, 'Nosso número inexistente', $guide);
            } else {
                // Retornar erro
                Vox::apiProblem('Payment not found.', self::NOT_FOUND, 9005, 'Registro não encontrado');
            }
        }

        // Salvar comunicação na base de dados
        if ($initialDate && $finalDate && $initialDate != '22032028') {
            $ws33 = new WS33Model();
            $ws33->orgao = $agreement;
            $ws33->consulta = $guide;
            $ws33->save();

            // Verificar se ocorreu erro ao salvar dados na base de dados
            if ($ws33->fail()) {
                // Retornar erro
                Vox::apiProblem('Error saving data to database.', self::INTERNAL_SERVER_ERROR, 9020, 'Erro interno');
            }
        }

        // Declarar variável que irá guardar os dados do pagamento
        $payments = [];

        // Organizar dados
        foreach ($siat->Body->consultaPagamentoResponse->return->Saida->SaidaConsultaPagamento->DadosPagamento as $payment) {
            $payments['pagamentos'][] = [
                'nu_nosso_numero' => $guide,
                'nu_valor_pago' => (float) $payment->valorPago,
                'dt_pagamento' => (string) $payment->dataPagamento,
                'co_autenticacao' => (string) $payment->codigoBarras
            ];
        }

        // Retornar pagamentos
        echo json_encode($payments);
    }
}