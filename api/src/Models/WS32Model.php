<?php

namespace Source\Models;

use LandKit\Model\Model;

class WS32Model extends Model
{
    /**
     * @var string
     */
    protected string $table = 'ws32';

    /**
     * @var array|string[]|null
     */
    protected ?array $required = [
        'orgao',
        'protocolo',
        'valor',
        'guia'
    ];

    /**
     * @const string
     */
    public const CREATED_AT = 'criado_em';

    /**
     * @const string
     */
    public const UPDATED_AT = '';
}
