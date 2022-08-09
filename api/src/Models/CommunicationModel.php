<?php

namespace Source\Models;

use LandKit\Model\Model;

class CommunicationModel extends Model
{
    /**
     * @var string
     */
    protected string $table = 'comunicacao';

    /**
     * @var array|string[]|null
     */
    protected ?array $required = [
        'ws',
        'json',
        'controle_orgao_id',
        'documento_cnpj',
        'documento_protocolo_redesim',
        'documento_tipo_modelo',
        'documento_situacao',
        'documento_evento_data',
        'ip'
    ];

    /**
     * @const string
     */
    const CREATED_AT = 'criado_em';

    /**
     * @const string
     */
    const UPDATED_AT = '';
}
