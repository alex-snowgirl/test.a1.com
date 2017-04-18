<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:32 PM
 */
namespace CORE\Api;

/**
 * Class Request
 * @package CORE\Api
 */
class Request extends \CORE\Request
{
    protected $method;

    /**
     * Facade method for itself
     */
    public function parse()
    {
        $this->iniClientID();
        $this->iniRequestMethod();
        $this->iniRequestParams();
    }

    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Simple function for client IP retrieving
     * @todo improve
     *
     * @return mixed
     */
    protected function iniClientID()
    {
        $this->clientId = $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Simple function for method retrieving
     * @todo improve
     *
     * @return mixed
     */
    protected function iniRequestMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        return $this;
    }

    /**
     * Simple function for params retrieving
     * @todo improve
     *
     * @return array
     */
    public function iniRequestParams()
    {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $script = trim($_SERVER['SCRIPT_NAME'], '/');

        if (0 === strpos($uri, $script)) {
            $uri = substr($uri, strlen($script));
        }

        $tmp = explode('?', $uri);
        $pathExplode = explode('/', trim($tmp[0], '/'));

        if (1 == sizeof($pathExplode) % 2) {
            $pathExplode[] = '';
        }

        $pathParams = array();

        for ($i = 0, $l = sizeof($tmp); $i < $l; $i += 2) {
            $pathParams[$pathExplode[$i]] = $pathExplode[$i + 1];
        }

        parse_str(file_get_contents("php://input"), $stream);

        $this->params = $pathParams + $_GET + $_POST + $_REQUEST + $stream;

        return $this;
    }
}
