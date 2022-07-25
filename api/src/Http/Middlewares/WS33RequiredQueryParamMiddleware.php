<?php

namespace Source\Http\Middlewares;

use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;

class WS33RequiredQueryParamMiddleware implements HttpResponseCodeInterface
{
    /**
     * Responsável por verificar se os parâmetros de consulta obrigatórios estão
     * presentes na requisição.
     *
     * @return bool
     */
    public function handle(): bool
    {
        // Obter parâmetros de consulta
        $queryParams = Route::getQueryParams();

        debug($queryParams);
    }
}