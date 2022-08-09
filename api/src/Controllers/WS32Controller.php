<?php

namespace Source\Controllers;

use Crell\ApiProblem\ApiProblem;
use LandKit\Route\Route;
use Source\Http\Interfaces\HttpResponseCodeInterface;
use Source\Models\WS32Model;
use Source\Support\Siat;
use Source\Support\Vox;

class WS32Controller implements HttpResponseCodeInterface
{
    /**
     * Responsável por receber a comunicação do WS 32.
     *
     * @return void
     */
    public function index(): void
    {
        // Obter parâmetros de consulta
        $queryParams = Route::getQueryParams();

        // Parâmetros de consulta obrigatórios
        $requiredQueryParams = [
            'nu_identificador_orgao',
            'nu_tipo_operacao',
            'co_protocolo',
            'co_cpf_cnpj',
            'ds_nome',
            'co_pais'
        ];

        // Verificar os parâmetros de consulta obrigatórios
        foreach ($requiredQueryParams as $param) {
            if (!isset($queryParams[$param])) {
                Vox::apiProblem(
                    'Query parameters not informed.',
                    self::BAD_REQUEST,
                    9003,
                    "Esta valor não deve ser nulo. {$param}"
                );
            }
        }

        // Obter parâmetro de consulta
        $organIdentifier = $queryParams['nu_identificador_orgao'];
        $operationType = $queryParams['nu_tipo_operacao'];
        $protocol = $queryParams['co_protocolo'];
        $cpfCnpj = $queryParams['co_cpf_cnpj'];
        $name = $queryParams['ds_nome'];
        $feeValue = $queryParams['nu_valor_taxa'] ?? null;
        $tribute = $queryParams['co_tributo'] ?? null;
        $tributeDetail = $queryParams['co_tributo_detalhe'] ?? null;
        $county = $queryParams['co_municipio'] ?? null;
        $country = $queryParams['co_pais'];
        $dueDate = $queryParams['dt_vencimento'] ?? null;
        $drawnAddress = $queryParams['endereco_sacado'] ?? null;
        $fullAddress = $queryParams['ds_endereco_completo'] ?? null;

        // Preparar data de vencimento
        if ($dueDate) {
            $tmp = explode('-', $dueDate);
            $dueDate = $tmp[2] . $tmp[1] . $tmp[0];
        }

        // Validar os tipos de operação
        if (!in_array($operationType, [1, 2])) {
            Vox::apiProblem(
                'Invalid operation type.',
                self::BAD_REQUEST,
                9004,
                'Este valor não é válido. nu_tipo_operacao',
                $operationType
            );
        }

        // Preparar XML de consulta para o SIAT
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WebServiceGTM">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <urn:consultaCadastroEconomico>
                            <!--Optional:-->
                            <mensagem>
                                <![CDATA[
                                <Entrada>
                                    <EntradaConsultaCadastroEconomico>
                                    <cpfCnpj>' . $cpfCnpj . '</cpfCnpj>
                                    <inscricaoMunicipal></inscricaoMunicipal>
                                    </EntradaConsultaCadastroEconomico>
                                </Entrada>
                                ]]>
                            </mensagem>
                        </urn:consultaCadastroEconomico>
                    </soapenv:Body>
                </soapenv:Envelope>';

        // Realizar consulta ao SIAT
        $siat = (new Siat())->call($xml);
        debug($siat);
    }
}