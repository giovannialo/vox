<?php

namespace Source\Models;

use LandKit\Model\Model;

class WS33Model extends Model
{
    /**
     * @var string
     */
    protected string $table = 'ws33';

    /**
     * @var array|string[]|null
     */
    protected ?array $required = [
        'orgao',
        'consulta'
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
