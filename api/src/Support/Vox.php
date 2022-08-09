<?php

namespace Source\Support;

use Crell\ApiProblem\ApiProblem;

class Vox
{
    /**
     * Responsável por retornar a estrutura básica de um problema para os Web
     * Services da vox.
     *
     * @param string $title
     * @param int $httpResponseCode
     * @param int $coReturn
     * @param string $dsReturn
     * @param string $dsValue
     * @return void
     */
    public static function apiProblem(
        string $title,
        int $httpResponseCode,
        int $coReturn,
        string $dsReturn,
        string $dsValue = 'N/A'
    ): void {
        // Definir código de resposta
        http_response_code($httpResponseCode);

        // Preparar resposta
        $problem = new ApiProblem($title);
        $problem->setStatus($httpResponseCode);
        $problem['erros'] = [
            'co_retorno' => $coReturn,
            'ds_retorno' => $dsReturn,
            'ds_valor' => $dsValue
        ];

        // Retornar resposta
        echo $problem->asJson();

        // Finalizar execução
        exit;
    }
}