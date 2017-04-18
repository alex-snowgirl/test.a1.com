<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 7:33 PM
 */
namespace CORE\View;

use CORE\View;
use CORE\DataHolder;

/**
 * @todo implement interface
 *
 * Class HTML
 * @package CORE\View
 */
class HTML implements View
{
    public function output(DataHolder $holder)
    {
        header('Content-Type: text/html');
        echo $holder->body;
    }
}