<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:26 PM
 */
namespace CORE\Api;

/**
 * Class Response
 * @package CORE\Api
 */
class Response extends \CORE\Response
{
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
     * For purpose of simplicity we overload parent method instead of writing preSend() method
     */
    public function send()
    {
        header('HTTP/1.1 ' . $this->code . ' ' . $this->codeToText[$this->code]);
        parent::send();
    }
}