<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 7:41 PM
 */
use APP\Api\App;
use CORE\Storage\Session;
use CORE\View\JSON;

require_once '../ini.php';
$loader = require_once '../vendor/autoload.php';

/**
 * For simplicity - we use JSON format, even it is not hypermedia format
 * @todo create and implement JSON-LD view (http://json-ld.org/)
 * @todo create and implement Config object
 *
 * For simplicity - we only use SESSION storage
 */
new App($loader, new Session(), new JSON());