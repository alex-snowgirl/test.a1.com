<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 12:18 AM
 */
namespace CORE\Api\Action;

use CORE\Api\Action;
use CORE\Api\Response;

/**
 * Class Patch
 * @package CORE\Api\Action
 */
class Patch extends Action
{
    public function run(Response $response = null)
    {
        $this->entity->update();
        $output = $this->entity->read();

        if ($response) {
            $response->setCode(200)->setBody($output);
        }

        return $output;
    }
}