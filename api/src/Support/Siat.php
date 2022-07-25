<?php

namespace Source\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Siat
{
    private Client $client;

    /**
     * Siat construtor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://192.168.10.203:8080/dsf_mcz_gtm/services/WebServiceGTM',
            'timeout' => 6,
        ]);
    }

    /**
     * Responsável por realizar uma consulta aos web services do Siat.
     *
     * @param string $xml
     * @return void
     */
    public function call(string $xml): void
    {
        try {
            // Realizar requisição
            $response = $this->client->post('', [
                'headers' => ['Content-Type' => 'application/xml'],
                'body' => $xml
            ]);

            debug($response);
        } catch (GuzzleException $e) {
            debug($e);
        }
    }
}