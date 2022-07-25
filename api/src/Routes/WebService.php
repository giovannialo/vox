<?php

use LandKit\Route\Route;
use Source\Http\Middlewares\VerifyTokenMiddleware;
use Source\Http\Middlewares\WS03RequiredFieldMiddleware;
use Source\Http\Middlewares\WS04RequiredFieldMiddleware;

// Definir controlador
Route::controller('Source\Controllers');

// Definir sessão
Route::session('v1/integracao', VerifyTokenMiddleware::class);

// WS 02
Route::post('/empresas', function () {
    echo 'Em breve...';
});

// WS 03
Route::post('/status-documentos', 'WS03Controller:store', middleware: WS03RequiredFieldMiddleware::class);

// WS 04
Route::post('/solicitacoes-documentos', 'WS04Controller:store', middleware: WS04RequiredFieldMiddleware::class);

// WS 32
Route::get('/gerar-dar', 'WS32Controller:index');

// WS 33
Route::get('/pagamentos', 'WS33Controller:index');
