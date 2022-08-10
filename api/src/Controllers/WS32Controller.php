<?php

namespace Source\Controllers;

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

        // Preparar tipo de operação
        if ($operationType == 2) {
            $operationType = 'IM';
        } elseif ($operationType == 3) {
            $operationType = 'PE';
        } else {
            $operationType = 'CA';
        }

        // Preparar data de vencimento
        if ($dueDate) {
            $tmp = explode('-', $dueDate);
            $dueDate = $tmp[2] . $tmp[1] . $tmp[0];
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

        // Obter resposta retornada pelo WS
        $response = $siat
            ->Body
            ->consultaCadastroEconomicoResponse
            ->return
            ->Entrada
            ->EntradaGravaCadastroEconomico
            ->resposta;

        // Verificar se a consulta não retornou o resultado esperado
        if ($response != 0) {
            // Retornar erro
            Vox::apiProblem('Register not found.', self::NOT_FOUND, 9005, 'Registro não encontrado');
        }

        // Simplificar o acesso aos dados do cadastro econômico
        $economicRegister = $siat
            ->Body
            ->consultaCadastroEconomicoResponse
            ->return
            ->Entrada
            ->EntradaGravaCadastroEconomico;

        // Preparar XML de consulta para o siat
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WebServiceGTM">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <urn:geraLancamentoEspecializadoOutrosSistemas>
                            <!--Optional:-->
                            <mensagem>
                                <![CDATA[
                                <Entrada>
                                    <EntradaIdentificacaoCadastral>
                                        <sistemaLancamentoEspecializado>SAT</sistemaLancamentoEspecializado>
                                        <tipoOperacao>' . $operationType . '</tipoOperacao>
                                        <tipoCadastro>2</tipoCadastro>
                                        <identificacaoCadastro>' . $economicRegister->inscricaoMunicipal . '</identificacaoCadastro>
                                        <identificacaoOrigemLancamento>SLIM' . $organIdentifier . '-' . $protocol . '</identificacaoOrigemLancamento>
                                        <grupoSistemaOrigem>REDESIM-' . $organIdentifier . '</grupoSistemaOrigem>
                                        <processoAdministrativo>1</processoAdministrativo>
                                        <volume/>
                                        <folha/>
                                        <EntradaLancamentoOutroSistema>
                                            <tributo>' . $tribute . '</tributo>
                                            <anoExercicio>' . date('Y') . '</anoExercicio>
                                            <dataBaseLancamento>' . date('dmY') . '</dataBaseLancamento>
                                            <dataNotificacao>' . date('dmY') . '</dataNotificacao>
                                            <qtdeParcelas>1</qtdeParcelas>
                                            <valorLancado>' . $feeValue . '</valorLancado>
                                            <valorLancadoMoeda>' . $feeValue . '</valorLancadoMoeda>
                                            <observacao>REDESIM - ' . $organIdentifier . ' - ' . $protocol . '</observacao>
                                            <estrutura>MEMORIA ORDITI</estrutura>
                                            <EntradaParcelaOutroSistema>
                                                <numero>1</numero>
                                                <dataBaseLancamento>' . date('dmY') . '</dataBaseLancamento>
                                                <dataVencimento>' . $dueDate . '</dataVencimento>
                                                <valorLancado>' . $feeValue . '</valorLancado>
                                                <valorLancadoMoeda>' . $feeValue . '</valorLancadoMoeda>
                                                <EntradaItemParcelaOutroSistema>
                                                    <itemTributo>' . $tributeDetail . '</itemTributo>
                                                    <siglaIndicadorEconomico>R$</siglaIndicadorEconomico>
                                                    <principal>S</principal>
                                                    <valorImposto>' . $feeValue . '</valorImposto>
                                                    <valorBeneficio>0</valorBeneficio>
                                                    <valorLancado>' . $feeValue . '</valorLancado>
                                                    <valorLancadoMoeda>' . $feeValue . '</valorLancadoMoeda>
                                                </EntradaItemParcelaOutroSistema>
                                            </EntradaParcelaOutroSistema>
                                            <EntradaMemoriaCalculo>
                                                <atributo>CODIGO ORDITI</atributo>
                                                <valor>REDESIM - ' . $organIdentifier . ' - ' . $protocol . '</valor>
                                            </EntradaMemoriaCalculo>
                                        </EntradaLancamentoOutroSistema>
                                        <EntradaPessoa>
                                            <cpfCnpj>' . $cpfCnpj . '</cpfCnpj>
                                            <nomeRazaoSocial>xxx</nomeRazaoSocial>
                                            <nomeRazaoSocialResumido>xxx</nomeRazaoSocialResumido>
                                            <tipoEnderecoEntregaPessoa>R</tipoEnderecoEntregaPessoa>
                                            <tipoDocumento></tipoDocumento>
                                            <numeroDocumento></numeroDocumento>
                                            <orgaoExpedidor></orgaoExpedidor>
                                            <ufOrgaoExpedidor></ufOrgaoExpedidor>
                                            <dataExpedicao></dataExpedicao>
                                            <dataNascimento></dataNascimento>
                                            <paisNaturalidade>BRASIL</paisNaturalidade>
                                            <estadoCivil>1</estadoCivil>
                                            <sexo></sexo>
                                            <EntradaEndereco>
                                                <tipoEndereco>R</tipoEndereco>
                                                <dddCelular/>
                                                <celular/>
                                                <tipoLocalizacao></tipoLocalizacao>
                                                <pais>BRASIL</pais>
                                                <tipoLogradouro></tipoLogradouro>
                                                <logradouro>Rua xxx</logradouro>
                                                <numero>1</numero>
                                                <complemento/>
                                                <tipoBairro>BAIRRO</tipoBairro>
                                                <bairro>xxx</bairro>
                                                <distrito/>
                                                <cidade>Rio Largo</cidade>
                                                <uf>AL</uf>
                                                <cep>57030-000</cep>
                                                <enderecoReferencia/>
                                                <zipCode/>
                                                <inscricaoImobiliaria/>
                                                <direcao/>
                                                <povoado/>
                                                <zonaRural/>
                                                <ccir/>
                                                <nirf/>
                                                <datum/>
                                                <latitude/>
                                                <longitude/>
                                                <dddTelefone/>
                                                <telefone/>
                                                <dddFax/>
                                                <fax/>
                                                <email/>	
                                            </EntradaEndereco>
                                        </EntradaPessoa>
                                    </EntradaIdentificacaoCadastral>	
                                </Entrada>
                                ]]>
                            </mensagem>
                    </urn:geraLancamentoEspecializadoOutrosSistemas>
                    </soapenv:Body>
                </soapenv:Envelope>';

        // Realizar consulta ao SIAT
        $siat = (new Siat())->call($xml);

        // Simplificar o acesso aos dados de lançamento especializado
        $specializedRelease = $siat
            ->Body
            ->geraLancamentoEspecializadoOutrosSistemasResponse
            ->return
            ->Saida
            ->SaidaLancamentoOutroSistema;

        // Verificar se não existe lançamento
        if (empty($specializedRelease->codigoLancamento)) {
            // Retornar erro
            Vox::apiProblem('launch not found.', self::NOT_FOUND, '9005', 'Registro não encontrado');
        }

        // Obter tipo de layout da guia
        if (isset($_GET['tipo']) && $_GET['tipo'] == 'N') {
            $guideLayout = 'N';
        } else {
            $guideLayout = 'D';
        }

        // Verificar se não deve retornar a guia em formato pdf
        if (isset($_GET['pdf']) && $_GET['pdf'] == 'no') {
            $returnPdf = 'N';
        } else {
            $returnPdf = 'S';
        }

        // Preparar XML de consulta para o SIAT
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WebServiceGTM">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <urn:solicitaGuiaDam>
                            <!--Optional:-->
                            <mensagem>
                                <![CDATA[
                                <Entrada>
                                    <EntradaSolicitaGuiaDam>
                                    <codigoCadastro>3</codigoCadastro>
                                    <numeroCadastro>' . $economicRegister->inscricaoMunicipal . '</numeroCadastro>
                                    <tipoProcedencia>EM</tipoProcedencia>
                                    <retornaPDF>' . $returnPdf . '</retornaPDF>
                                    <tipoLayoutGuia>' . $guideLayout . '</tipoLayoutGuia>
                                    <emissorPortal>S</emissorPortal>
                                    <guiaPorParcela>S</guiaPorParcela>
                                    <dataCalculo>' . date('dmY') . '</dataCalculo>	
                                    <entradaSolicitaGuiaDamParcela>
                                        <anoExercicio>' . date('Y') . '</anoExercicio>
                                        <codigoLancamento>' . $specializedRelease->codigoLancamento . '</codigoLancamento>
                                        <codigoTributo></codigoTributo>			
                                        <entradaSolicitaGuiaDamParcelaNumero>
                                            <numeroParcela>1</numeroParcela>
                                        </entradaSolicitaGuiaDamParcelaNumero>		
                                    </entradaSolicitaGuiaDamParcela>
                                    </EntradaSolicitaGuiaDam>
                                </Entrada>
                                ]]>
                            </mensagem>
                        </urn:solicitaGuiaDam>
                    </soapenv:Body>
                </soapenv:Envelope>';

        // Realizar consulta ao SIAT
        $siat = (new Siat())->call($xml);

        // Obter resposta retornada pelo WS
        $response = $siat
            ->Body
            ->solicitaGuiaDamResponse
            ->return
            ->Saida
            ->SaidaSolicitaGuiaDam
            ->resposta;

        // Verificar se a consulta não retornou o resultado esperado
        if ($response != 0) {
            // Retornar erro
            Vox::apiProblem('The dam tab was not generated.', self::NOT_FOUND, 9005, 'Registro não encontrado');
        }

        // Simplificar o acesso aos dados de lançamento especializado
        $guideDam = $siat
            ->Body
            ->solicitaGuiaDamResponse
            ->return
            ->Saida
            ->SaidaSolicitaGuiaDam
            ->SaidaSolicitaGuiaDamArquivos;

        // === === === === === === === === === ===
        // Gerar imagem e data de vencimento aqui
        // === === === === === === === === === ===

        // Preparar dados para retorno
        $fee['taxas'][] = [
            'nu_nosso_numero' => (string) $guideDam->codigoGuiaDam,
            'nu_linha_digitavel' => (string) $guideDam->linhaDigitavel,
            'nu_representacao_numerica' => (string) $guideDam->codigoBarras,
            'dt_vencimento' => date_format(date_create(substr($guideDam->codigoBarras, 19, 8)), 'Y-m-d'),
            'ds_url_taxa' => (string) $guideDam->arquivo
        ];

        // Salvar comunicação no banco de dados
        $ws32 = new WS32Model();
        $ws32->orgao = $organIdentifier;
        $ws32->protocolo = $protocol;
        $ws32->valor = $feeValue;
        $ws32->guia = (string) $guideDam->codigoGuiaDam;
        $ws32->save();

        // Verificar se ocorreu erro ao salvar na base de dados
        if ($ws32->fail()) {
            // Retornar erro
            Vox::apiProblem('Error saving data to database.', self::INTERNAL_SERVER_ERROR, 9020, 'Erro interno');
        }

        // Retornar guia
        echo json_encode($fee);
    }
}