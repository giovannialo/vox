<?php

namespace Source\Support;

use Crell\ApiProblem\ApiProblem;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SimpleXMLElement;
use Source\Http\Interfaces\HttpResponseCodeInterface;

class Siat implements HttpResponseCodeInterface
{
    private Client $client;

    /**
     * Siat construtor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => CONF_SIAT_WEB_SERVICE_URL,
            'timeout' => 7,
        ]);
    }

    /**
     * Responsável por realizar consulta ao web service do Siat.
     *
     * @param string $xml
     * @return SimpleXMLElement
     */
    public function call(string $xml): SimpleXMLElement
    {
        try {
            // Realizar requisição
            $response = $this->client->post('', [
                'headers' => ['Content-Type' => 'application/xml'],
                'body' => $xml
            ]);

            // Obter resposta
            $xml = $response->getBody()->getContents();

            // Tratar resposta
            $xml = str_replace(['soap:', 'ns2:'], '', $xml);
            $xml = utf8_encode(str_replace(['&lt;', '&gt;'], ['<', '>'], $xml));

            // Retornar resposta em xml
            return simplexml_load_string($xml);
        } catch (GuzzleException $e) {
            // Definir código de resposta
            http_response_code(self::INTERNAL_SERVER_ERROR);

            // Retornar resposta
            echo (new ApiProblem('A definir'))
                ->setStatus(self::INTERNAL_SERVER_ERROR)
                ->setDetail($e->getMessage())
                ->asJson();

            // Finalizar execução
            exit;
        }
    }
}