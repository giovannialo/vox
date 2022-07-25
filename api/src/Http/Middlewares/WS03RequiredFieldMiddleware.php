<?php

namespace Source\Http\Middlewares;

use Crell\ApiProblem\ApiProblem;
use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;

class WS03RequiredFieldMiddleware implements HttpResponseCodeInterface
{
    /**
     * Responsável por verificar se os campos obrigatórios estão presentes na
     * requisição.
     *
     * @return bool
     */
    public function handle(): bool
    {
        // Obter corpo da requisição
        $bodyData = Route::getJsonData();

        // Verificar se o campo 'nu_identificador_orgao' não foi informado/preenchido
        if (empty($bodyData['controle']['nu_identificador_orgao'])) {
            $this->problem('controle.nu_identificador_orgao');
        }

        // Verificar se o campo 'co_protocolo_redesim' não foi informado/preenchido
        if (empty($bodyData['dados_documento']['co_protocolo_redesim'])) {
            $this->problem('dados_documento.co_protocolo_redesim');
        }

        // Verificar se o campo 'co_tipo_modelo_documento' não foi informado/preenchido
        if (empty($bodyData['dados_documento']['co_tipo_modelo_documento'])) {
            $this->problem('dados_documento.co_tipo_modelo_documento');
        }

        // Verificar se o campo 'co_situacao' não foi informado/preenchido
        if (empty($bodyData['dados_documento']['co_situacao'])) {
            $this->problem('dados_documento.co_situacao');
        }

        // Verificar se o campo 'dt_evento' não foi informado/preenchido
        if (empty($bodyData['dados_documento']['dt_evento'])) {
            $this->problem('dados_documento.dt_evento');
        }

        // Continuar fluxo
        return true;
    }

    /**
     * Responsável por retornar uma resposta de erro.
     *
     * @param string $fieldName
     * @return never
     */
    private function problem(string $fieldName): never
    {
        // Preparar resposta
        $problem = new ApiProblem('Required field not filled.');
        $problem->setStatus(self::BAD_REQUEST);
        $problem['erros'] = [
            'co_retorno' => 9003,
            'ds_retorno' => "the field '{$fieldName}' is required.",
            'ds_valor' => 'N/A',
        ];

        // Retornar resposta
        echo $problem->asJson();

        // Finalizar script
        exit;
    }
}