<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:25 PM
 */
namespace CORE;

/**
 * @todo improve
 * Class Request
 * @package CORE
 */
abstract class Request
{
    abstract public function parse();

    protected $params = array();

    public function getParam($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return null;
    }

    public function getParams()
    {
        return $this->params;
    }

    protected $clientId;

    public function getClientId()
    {
        return $this->clientId;
    }
}