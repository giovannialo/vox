<?php

namespace Source\Http\Interfaces;

interface HttpResponseCodeInterface
{
    /**
     * @const int
     */
    const OK = 200;

    /**
     * @const int
     */
    const CREATED = 201;

    /**
     * @const int
     */
    const ACCEPTED = 202;

    /**
     * @const int
     */
    const NO_CONTENT = 204;

    /**
     * @const int
     */
    const BAD_REQUEST = 400;

    /**
     * @const int
     */
    const UNAUTHORIZED = 401;

    /**
     * @const int
     */
    const PAYMENT_REQUIRED = 402;

    /**
     * @const int
     */
    const FORBIDDEN = 403;

    /**
     * @const int
     */
    const NOT_FOUND = 404;

    /**
     * @const int
     */
    const METHOD_NOT_ALLOWED = 405;

    /**
     * @const int
     */
    const NOT_ACCEPTABLE = 406;

    /**
     * @const int
     */
    const PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * @const int
     */
    const REQUEST_TIMEOUT = 408;

    /**
     * @const int
     */
    const CONFLICT = 409;

    /**
     * @const int
     */
    const GONE = 410;

    /**
     * @const int
     */
    const LENGTH_REQUIRED = 411;

    /**
     * @const int
     */
    const PRECONDITION_FAILED = 412;

    /**
     * @const int
     */
    const PAYLOAD_TOO_LARGE = 413;

    /**
     * @const int
     */
    const URI_TOO_LONG = 414;

    /**
     * @const int
     */
    const UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * @const int
     */
    const RANGE_NOT_SATISFIABLE = 416;

    /**
     * @const int
     */
    const EXPECTATION_FAILED = 417;

    /**
     * @const int
     */
    const IM_A_TEAPOT = 418;

    /**
     * @const int
     */
    const MISDIRECTED_REQUEST = 421;

    /**
     * @const int
     */
    const UNPROCESSABLE_ENTITY = 422;

    /**
     * @const int
     */
    const LOCKED = 423;

    /**
     * @const int
     */
    const FAILED_DEPENDENCY = 424;

    /**
     * @const int
     */
    const UPGRADE_REQUIRED = 426;

    /**
     * @const int
     */
    const PRECONDITION_REQUIRED = 428;

    /**
     * @const int
     */
    const TOO_MANY_REQUESTS = 429;

    /**
     * @const int
     */
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * @const int
     */
    const UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    /**
     * @const int
     */
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * @const int
     */
    const NOT_IMPLEMENTED = 501;

    /**
     * @const int
     */
    const BAD_GATEWAY = 502;

    /**
     * @const int
     */
    const SERVICE_UNAVAILABLE = 503;

    /**
     * @const int
     */
    const GATEWAY_TIMEOUT = 504;

    /**
     * @const int
     */
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * @const int
     */
    const VARIANT_ALSO_NEGOTIATES = 506;

    /**
     * @const int
     */
    const INSUFFICIENT_STORAGE = 507;

    /**
     * @const int
     */
    const LOOP_DETECTED = 508;

    /**
     * @const int
     */
    const NOT_EXTENDED = 510;

    /**
     * @const int
     */
    const NETWORK_AUTHENTICATION_REQUIRED = 511;
}
