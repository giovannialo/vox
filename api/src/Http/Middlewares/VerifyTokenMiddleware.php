<?php

namespace Source\Http\Middlewares;

use Crell\ApiProblem\ApiProblem;
use Source\Http\Interfaces\HttpResponseCodeInterface;

class VerifyTokenMiddleware implements HttpResponseCodeInterface
{
    /**
     * Responsável por verificar e validar o header auth-token.
     *
     * @return bool
     */
    public function handle(): bool
    {
        // Obter headers da requisição
        $headers = getallheaders();

        // Verificar se o header auth-token não foi informado
        if (!$headers || !isset($headers['auth-token'])) {
            // Definir código de resposta
            http_response_code(self::UNAUTHORIZED);

            // Preparar resposta
            $problem = new ApiProblem('Invalid auth-token.');
            $problem->setStatus(self::UNAUTHORIZED);
            $problem['erros'] = [
                'co_retorno' => 9006,
                'ds_retorno' => 'auth-token header does not exist.',
                'ds_valor' => 'N/A',
            ];

            // Retornar resposta
            echo $problem->asJson();

            // Finalizar execução
            exit;
        }

        // Verificar se o token informado não é válido
        if (!in_array($headers['auth-token'], CONF_WEB_SERVICE_TOKEN_CLIENT)) {
            // Definir código de resposta
            http_response_code(self::UNAUTHORIZED);

            // Preparar resposta
            $problem = new ApiProblem('Invalid auth-token.');
            $problem->setStatus(self::UNAUTHORIZED);
            $problem['erros'] = [
                'co_retorno' => 9006,
                'ds_retorno' => 'auth-token is invalid.',
                'ds_valor' => 'N/A',
            ];

            // Retornar resposta
            echo $problem->asJson();

            // Finalizar execução
            exit;
        }

        // Continuar fluxo
        return true;
    }
}
