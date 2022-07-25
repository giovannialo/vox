<?php

namespace Source\Controllers;

use Crell\ApiProblem\ApiProblem;
use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;
use Source\Models\CommunicationModel;

class WS03Controller implements HttpResponseCodeInterface
{
    /**
     * Responsável por receber a comunicação do WS 03.
     *
     * @return void
     */
    public function store(): void
    {
        // Obter corpo da requisição
        $bodyData = Route::getJsonData();

        // Instanciar modelo de comunicação
        $communication = new CommunicationModel();

        // Preparar dados para salvar na base de dados
        $communication->ws = 03;
        $communication->json = json_encode($bodyData);
        $communication->controle_orgao_id = $bodyData['controle']['nu_identificador_orgao'];
        $communication->documento_protocolo_redesim = $bodyData['dados_documento']['co_protocolo_redesim'];
        $communication->documento_tipo_modelo = $bodyData['dados_documento']['co_tipo_modelo_documento'];
        $communication->documento_situacao = $bodyData['dados_documento']['co_situacao'];
        $communication->documento_evento_data = $bodyData['dados_documento']['dt_evento'];
        $communication->ip = getClientIp();

        // Salvar dados na base de dados
        $communication->save();

        // Verificar se ocorreu erro ao salvar dados na base de dados
        if ($communication->fail()) {
            // Definir código de resposta
            http_response_code(self::INTERNAL_SERVER_ERROR);

            // Preparar resposta
            $problem = new ApiProblem('Error saving data to database.');
            $problem->setStatus(self::INTERNAL_SERVER_ERROR);
            $problem['erros'] = [
                'co_retorno' => 9920,
                'ds_retorno' => $communication->fail()->getMessage(),
                'ds_valor' => 'N/A'
            ];

            // Retornar resposta
            echo $problem->asJson();

            // Finalizar script
            exit;
        }

        // Adicionar id gerado na resposta
        $bodyData['id'] = $communication->id;

        // Definir código de resposta
        http_response_code(self::CREATED);

        // Retornar resposta
        echo json_encode($bodyData);
    }
}
