<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:13 PM
 */
use CORE\Web\App;
use CORE\Storage\Session;
use CORE\Web\View\HTML;

require_once '../ini.php';
$loader = require_once '../vendor/autoload.php';

/**
 * For simplicity - we only use SESSION storage
 * @todo create and implement Config object
 */
new App($loader, new Session(), new HTML());