<?php

namespace Source\Controllers;

use Crell\ApiProblem\ApiProblem;
use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;
use Source\Models\CommunicationModel;
use Source\Support\Vox;

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
        $communication->documento_cnpj = $bodyData['dados_documento']['nu_cnpj'];
        $communication->documento_protocolo_redesim = $bodyData['dados_documento']['co_protocolo_redesim'];
        $communication->documento_tipo_modelo = $bodyData['dados_documento']['co_tipo_modelo_documento'];
        $communication->documento_situacao = $bodyData['dados_documento']['co_situacao'];
        $communication->documento_evento_data = $bodyData['dados_documento']['dt_evento'];
        $communication->ip = getClientIp();

        // Salvar dados na base de dados
        $communication->save();

        // Verificar se ocorreu erro ao salvar dados na base de dados
        if ($communication->fail()) {
            // Retornar erro
            Vox::apiProblem('Error saving data to database.', self::BAD_REQUEST, 9920, 'Erro interno');
        }

        // Adicionar id gerado na resposta
        $bodyData['id'] = $communication->id;

        // Definir código de resposta
        http_response_code(self::CREATED);

        // Retornar resposta
        echo json_encode($bodyData);
    }
}
