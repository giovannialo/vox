<?php

use Crell\ApiProblem\ApiProblem;
use LandKit\DotEnv\DotEnv;
use Source\Http\Interfaces\HttpResponseCodeInterface;


/* *** *** *** *** *** *** *** *** *** ***
 *
 * Variáveis de ambiente
 *
 * *** *** *** *** *** *** *** *** *** ***/

// Verificar se o arquivo .env não existe
if (!DotEnv::load(__DIR__ . '/../../')) {
    // Definir código de resposta
    http_response_code(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR);

    // Retornar resposta
    echo (new ApiProblem('Error loading .env file.'))->setStatus(500)->asJson();

    // Finalizar execução
    exit;
}


/* *** *** *** *** *** *** *** *** *** ***
 *
 *  Url do sistema
 *
 * *** *** *** *** *** *** *** *** *** ***/

// Verificar se o HTTPS está habilitado
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : null);

// Definir constante com URL do sistema
define('CONF_BASE_URL', "http{$https}://" . getenv('HOST'));


/* *** *** *** *** *** *** *** *** *** ***
 *
 * Banco de dados
 *
 * *** *** *** *** *** *** *** *** *** ***/

// Obter as variáveis de ambiente
$databaseKey = getenv('DATABASE_KEY');
$databaseDriver = getenv('DATABASE_DRIVER');
$databaseHost = getenv('DATABASE_HOST');
$databasePort = getenv('DATABASE_PORT');
$databaseDbName = getenv('DATABASE_DBNAME');
$databaseUsername = getenv('DATABASE_USERNAME');
$databasePassword = getenv('DATABASE_PASSWORD');
$databaseOptions = getenv('DATABASE_OPTIONS');

// Verificar se alguma variável está vazia
if (!$databaseKey || !$databaseDriver || !$databaseHost || !$databasePort
    || !$databaseDbName || !$databaseUsername || !$databasePassword
) {
    // Definir código de resposta
    http_response_code(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR);

    // Retornar resposta
    echo (new ApiProblem('Error loading database configuration.'))
        ->setStatus(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR)
        ->asJson();

    // Finalizar execução
    exit;
}

// Preparar configuração
$database = [];
$databaseKey = explode(';;', $databaseKey);
$databaseDriver = explode(';;', $databaseDriver);
$databaseHost = explode(';;', $databaseHost);
$databasePort = explode(';;', $databasePort);
$databaseDbName = explode(';;', $databaseDbName);
$databaseUsername = explode(';;', $databaseUsername);
$databasePassword = explode(';;', $databasePassword);
$databaseOptions = explode(';;', $databaseOptions);

// Estruturar configuração
foreach ($databaseKey as $i => $value) {
    // Verificar se alguma configuração não existe ou se está no formato incorreto
    if (!isset($databaseDriver[$i]) || !isset($databaseHost[$i]) || !isset($databasePort[$i])
        || !isset($databaseDbName[$i]) || !isset($databaseUsername[$i]) || !isset($databasePassword[$i])
        || !isset($databaseOptions[$i]) || !is_numeric($databasePort[$i])
    ) {
        // Definir código de resposta
        http_response_code(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR);

        // Retornar resposta
        echo (new ApiProblem('Error loading database configuration.'))
            ->setStatus(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR)
            ->asJson();

        // Finalizar execução
        exit;
    }

    // Indexar configuração
    $database[$value] = [
        'driver' => $databaseDriver[$i],
        'host' => $databaseHost[$i],
        'port' => (int) $databasePort[$i],
        'dbname' => $databaseDbName[$i],
        'username' => $databaseUsername[$i],
        'password' => $databasePassword[$i]
    ];

    // Verificar se existem configurações de comportamentos
    if ($databaseOptions[$i]) {
        parse_str($databaseOptions[$i], $options);
        $database[$value]['options'] = array_map(fn($item) => (is_numeric($item) ? (int) $item : $item), $options);
    }
}

// Definir constante com configuração do banco de dados
define('CONF_DATABASE', $database);


/* *** *** *** *** *** *** *** *** *** ***
 *
 * Web service - Token cliente-servidor
 *
 * *** *** *** *** *** *** *** *** *** ***/

// Obter as variáveis de ambiente
$webServiceTokenClient = getenv('WEB_SERVICE_TOKEN_CLIENT');
$webServiceTokenServer = getenv('WEB_SERVICE_TOKEN_SERVER');

// Verificar se alguma variável de token está vazia
if (!$webServiceTokenClient || !$webServiceTokenServer) {
    // Definir código de resposta
    http_response_code(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR);

    // Retornar resposta
    echo (new ApiProblem('Error loading web service token configuration.'))
        ->setStatus(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR)
        ->asJson();

    // Finalizar execução
    exit;
}

// Preparar configuração
$webServiceTokenClient = explode(';;', $webServiceTokenClient);
$webServiceTokenServer = explode(';;', $webServiceTokenServer);

// Definir constantes com configuração dos tokens
define('CONF_WEB_SERVICE_TOKEN_CLIENT', $webServiceTokenClient);
define('CONF_WEB_SERVICE_TOKEN_SERVER', $webServiceTokenServer);


/* *** *** *** *** *** *** *** *** *** ***
 *
 * Siat
 *
 * *** *** *** *** *** *** *** *** *** ***/

// Obter as variáveis de ambiente
$siatWebServiceUrl = getenv('SIAT_WEB_SERVICE_URL');

// Verificar se a URL do web service está vazia
if (!$siatWebServiceUrl) {
    // Definir código de resposta
    http_response_code(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR);

    // Retornar resposta
    echo (new ApiProblem('Error loading web service url configuration.'))
        ->setStatus(HttpResponseCodeInterface::INTERNAL_SERVER_ERROR)
        ->asJson();

    // Finalizar execução
    exit;
}

// Definir constante com URL do web service
define('CONF_SIAT_WEB_SERVICE_URL', $siatWebServiceUrl);