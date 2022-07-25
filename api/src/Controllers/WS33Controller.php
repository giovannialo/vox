<?php

namespace Source\Controllers;

use LandKit\Route\Route;

class WS33Controller
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


    }
}