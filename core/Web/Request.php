<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:32 PM
 */
namespace CORE\Web;

/**
 * Dummy class
 *
 * Class Request
 * @package CORE\Web
 */
class Request extends \CORE\Request
{
    public function parse()
    {
        return $this;
    }
}