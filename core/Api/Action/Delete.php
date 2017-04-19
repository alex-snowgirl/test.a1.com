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
 * Class Delete
 * @package CORE\Api\Action
 */
class Delete extends Action
{
    public function run(Response $response = null)
    {
        $output = $this->entity->delete();

        if ($response) {
            $response->setCode(204)->setBody(array());
        }

        return $output;
    }
}