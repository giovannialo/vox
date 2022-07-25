<?php

use Crell\ApiProblem\ApiProblem;
use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;

// Iniciar buffer de saída
ob_start();

// Importar autoloader
require __DIR__ . '/vendor/autoload.php';

// Inicializar roteador
Route::init(CONF_BASE_URL);

// Definir pasta de rotas como src/Routes
$path = __DIR__ . '/src/Routes';

// Verificar se existem arquivos de rotas na pasta
if ($dir = scandir($path)) {
    foreach ($dir as $file) {
        // Ignorar arquivos de diretório
        if ($file == '.' || $file == '..') {
            continue;
        }

        // Verificar se o arquivo existe e não é um diretório
        if (file_exists("{$path}/{$file}") && !is_dir("{$path}/{$file}")) {
            // Incluir arquivo de rota
            require_once "{$path}/{$file}";
        }
    }
}

// Executar roteador
Route::dispatch();

// Verificar se ocorreu algum erro
if (Route::fail()) {
    // Definir código de resposta
    http_response_code(Route::fail());

    // Definir título da resposta
    $title = match (Route::fail()) {
        HttpResponseCodeInterface::BAD_REQUEST => 'Bad request.',
        HttpResponseCodeInterface::FORBIDDEN => 'Forbidden.',
        HttpResponseCodeInterface::NOT_FOUND => 'Not found.',
        HttpResponseCodeInterface::METHOD_NOT_ALLOWED => 'Partially implemented.',
        HttpResponseCodeInterface::NOT_IMPLEMENTED => 'Not implemented.',
        default => 'Undefined.',
    };

    // Preparar resposta
    $problem = new ApiProblem($title);
    $problem->setStatus(Route::fail());
    $problem['date'] = date('Y-m-d H:i:s');
    $problem['path'] = (Route::current()->path ?: '/');

    // Retornar resposta
    echo $problem->asJson();

    // Finalizar script
    exit;
}

// Limpar buffer de saída
ob_end_flush();
