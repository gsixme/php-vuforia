<?php

namespace Gsix\Vuforia;

use Gsix\Vuforia\Request\TargetRequest;

/**
 * Service factory class for API resources in the root namespace.
 *
 * @property TargetRequest $targets
 */
class VuforiaClient
{
    private $requestHandlers = [];
    private $config = [];

    const DEFAULT_CONFIG = [

        'access_key' => null,
        'secret_key' => null,

        /*
        |--------------------------------------------------------------
        | Name checking rule. Default is
        | no spaces and may only contain:
        | numbers (0-9), letters (a-z), underscores ( _ ) and dashes ( - )
        |--------------------------------------------------------------
        |
        |
        */
        'naming_rule' => '/^[\w\-]+$/',


        /*
        |--------------------------------------------------------------
        | Max image size(unencoded) in Bit. Default is 2MB
        |--------------------------------------------------------------
        |
        |
        */
        'max_image_size' => 2097152,

        /*
        |--------------------------------------------------------------
        | Max metadata size(unencoded) in Bit. Default is 2MB
        |--------------------------------------------------------------
        |
        |
        */
        'max_meta_size' => 2097152,

        /*
        |--------------------------------------------------------------
        | Vuforia Web Service Base API Endpoint
        |--------------------------------------------------------------
        |
        |
        */
        'api_base' => 'https://vws.vuforia.com/',
    ];

    /**
     * @var array<string, string>
     */
    private static $classMap = [
        'targets' => TargetRequest::class,
    ];


    public function __construct($config = [])
    {
        if (!\is_array($config)) {
            throw new Exception('$config must be an array');
        }

        $config = \array_merge(self::DEFAULT_CONFIG, $config);

        $this->validateConfig($config);

        $this->config = $config;
    }

    private function validateConfig($config)
    {
        // access_key
        if (!isset($config['access_key']) || !\is_string($config['access_key'])) {
            throw new \Exception('access_key must be a string');
        }

        // secret_key
        if (!isset($config['secret_key']) || !\is_string($config['secret_key'])) {
            throw new \Exception('secret_key must be a string');
        }

        // api_base
        if (!\is_string($config['api_base'])) {
            throw new \Exception('api_base must be a string');
        }

        // max_image_size
        if (!isset($config['max_image_size']) || !is_numeric($config['max_image_size'])) {
            throw new Exception("max_image_size is missing / must be numeric");
        }

        // max_meta_size
        if (!isset($config['max_meta_size']) || !is_numeric($config['max_meta_size'])) {
            throw new Exception("max_meta_size is missing / must be numeric");
        }

        // naming_rule
        if (!isset($config['naming_rule'])) {
            throw new \Exception('naming_rule is missing');
        }

        // check absence of extra keys
        $extraConfigKeys = \array_diff(\array_keys($config), \array_keys(self::DEFAULT_CONFIG));
        if (!empty($extraConfigKeys)) {
            throw new Exception('Found unknown key(s) in configuration array: ' . \implode(',', $extraConfigKeys));
        }
    }

    public function __get($name)
    {
        $requestHandler = $this->getRequestHandler($name);
        if (null !== $requestHandler) {

            if (!\array_key_exists($name, $this->requestHandlers)) {
                $this->requestHandlers[$name] = new $requestHandler($this->config);
            }

            return $this->requestHandlers[$name];
        }

        \trigger_error('Undefined property: ' . static::class . '::$' . $name);

        return null;
    }

    protected function getRequestHandler($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }


}