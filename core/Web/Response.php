<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:26 PM
 */
namespace CORE\Web;

/**
 * Response for Web purposes
 *
 * Class Response
 * @package CORE\Web
 */
class Response implements \CORE\Response
{
    protected $code;
    protected $body;

    protected $codeToText = array(
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        429 => 'Too Many Requests',
    );

    /**
     * @var View
     */
    protected $view;

    /**
     * Constructor
     * View object - is a strategy of rendering
     *
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function send($die = false)
    {
        $this->addHeader('HTTP/1.1 ' . $this->code . ' ' . $this->codeToText[$this->code])
            ->sendHeaders();

        $view = $this->view->generateForClient($this);

        echo $view;

        if ($die) {
            die;
        }
    }

    protected $headers = array();

    /**
     * @param $header
     * @return Response
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    protected function sendHeaders()
    {
        foreach ($this->headers as $header) {
            header($header);
        }

        return $this;
    }
}